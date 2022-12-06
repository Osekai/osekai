<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

header("Content-Type: application/json");

function error_early_return($message, $code = 404) {
    http_response_code($code);
    echo json_encode($message);
    exit;
}

function get_comment($commentId) {
    $rows = Database::execSelect("SELECT * FROM Comments WHERE ID = ? LIMIT 1", "i", [$commentId]);

    if (count($rows) == 0)
        return null;
    
    return $rows[0];
}

class CommentQuery {
    private string $query;
    private string $typeSignature;

    function __construct(string $query, string $typeSignature)
    {
        $this->query = $query;
        $this->typeSignature = $typeSignature;
    }

    function execute(array $params) {
        Database::execOperation($this->query, $this->typeSignature, $params); 
    }
}

class CommentQueryBuilder {
    private ?string $section = null;
    private bool $hasParentCommentInfo = false;

    public function setSection(string $section) {
        $this->section = $section;
    }

    public function setHasParentCommentInfo(bool $hasParentCommentInfo) {
        $this->hasParentCommentInfo = $hasParentCommentInfo;
    }

    public function build(): CommentQuery {
        if (!isset($this->section))
            throw new Exception("Section is not specified");

        $query = "INSERT INTO Comments (PostText, " . $this->section . ", Username, UserID, AvatarURL, ";

        if ($this->hasParentCommentInfo)
            return new CommentQuery($query ."ParentComment, ParentCommenter, PostDate) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", "sisisis");
        else
            return new CommentQuery($query . "PostDate) VALUES (?, ?, ?, ?, ?, NOW())", "sisis");
    }

}

if(isset($_POST['bGetComments'])) {
    $colComments = array();
    $type = 0;
    $colname = "";
    $data = "";
    if(isset($_POST['strMedalID'])) {
        $type = 1;
        $colname = "MedalID";
        $data = $_POST['strMedalID'];
    } elseif(isset($_POST['nVersionId'])) {
        $type = 3;
        $colname = "VersionID";
        $data = $_POST['nVersionID'];
    } elseif(isset($_POST['nProfileId'])) {
        $type = 4;
        $colname = "ProfileID";
        $data = $_POST['nProfileId'];
    }
    // note: inserting the $colname variable directly into sql here
    // is fine, since it's set up there manually by code, and doesn't
    // actually take any user input. no worse than having the horrible
    // wall of if statements there was before. just much, much better.
    if(isset($_SESSION['osu']['id'])) {
        
        $colComments = Database::execSelect("SELECT Comments.ID " .
            ", Comments.PostText " .
            ", Comments.UserID " . 
            ", Comments.PostDate " .
            ", Comments.ParentCommenter " .
            ", Coalesce(Comments.ParentComment, 0) AS Parent " .
            ", Comments.Username " .
            ", Comments.AvatarURL " .
            ", Comments.".$colname." AS MedalID " .
            ", GROUP_CONCAT(DISTINCT GroupAssignments.GroupId SEPARATOR ',') as Groups" .
            ", (SELECT Votes.Vote FROM Votes WHERE Votes.UserID = ? AND Votes.ObjectID = Comments.ID AND Votes.Type = ?) AS HasVoted " .
            ", (SELECT SUM(Votes.Vote) FROM Votes WHERE Votes.ObjectID = Comments.ID AND Votes.Type = ?) AS VoteSum "  .
        "FROM Comments " . 
        "LEFT JOIN Votes ON Votes.ObjectID = Comments.ID AND Votes.Type = ? " . 
        "LEFT JOIN GroupAssignments ON GroupAssignments.UserId = Comments.UserID " . 
        "WHERE ".$colname." = ? " . 
        "GROUP BY Comments.ID, Comments.PostText, Comments.UserID, Comments.PostDate, Comments.ParentCommenter, Comments.".$colname.", Comments.ParentComment", "iiiii", array($_SESSION['osu']['id'], $type, $type, $type, $data));
    } else {
        $colComments = Database::execSelect("SELECT 
        Comments.ID, 
        Comments.PostText, 
        Comments.UserID, 
        Comments.PostDate, 
        Comments.ParentCommenter, 
        Coalesce(Comments.ParentComment, 0) AS Parent, 
        Comments.Username, Comments.AvatarURL, 
        Comments.".$colname." AS MedalID, 
        GROUP_CONCAT(DISTINCT GroupAssignments.GroupId SEPARATOR ',') as Groups,
        (SELECT SUM(Votes.Vote) FROM Votes WHERE Votes.ObjectID = Comments.ID AND Votes.Type = ?) AS VoteSum
        FROM Comments LEFT JOIN Votes ON 
        Votes.ObjectID = Comments.ID AND 
        Votes.Type = ?
        LEFT JOIN GroupAssignments ON GroupAssignments.UserId = Comments.UserID WHERE ".$colname." = ? 
        GROUP BY Comments.ID, Comments.PostText, Comments.UserID, Comments.PostDate, Comments.ParentCommenter, Comments.".$colname.", Comments.ParentComment", "iii", array($type, $type, $data));
    }
    echo json_encode($colComments);
    // Prevent other actions 
    exit;
}

if(isset($_POST['strComment'])) {
    if(isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        $commentQueryBuilder = new CommentQueryBuilder();
        $sectionId;
        
        if(isset($_POST['strCommentMedalID'])) {
            $commentQueryBuilder->setSection("MedalID");
            $sectionId = $_POST['strCommentMedalID'];
        } elseif (isset($_POST['nVersionId'])) {
            $commentQueryBuilder->setSection("VersionId");
            $sectionId = $_POST['nVersionId'];
        } elseif (isset($_POST['nProfileId'])) {
            $commentQueryBuilder->setSection("ProfileID");
            $sectionId = $_POST['nProfileId'];
        } else {
            error_early_return("Invalid section");
        }


        if(isset($_POST['nParentComment'])) {
            $commentQueryBuilder->setHasParentCommentInfo(true);

            $comment = get_comment(intval($_POST['nParentComment']));

            if (!isset($comment))
                error_early_return("ParentComment does not exist");

            if ($comment['Username'] != $_POST['strParentCommenter'])
                error_early_return("ParentCommenter name mismatches");
         
            $commentQueryBuilder->build()->execute(array($_POST['strComment'], intval($sectionId), $_SESSION['osu']['username'], $_SESSION['osu']['id'], $_SESSION['osu']['avatar_url'], $_POST['nParentComment'], $_POST['strParentCommenter']));
        } else {
            $commentQueryBuilder->build()->execute(array($_POST['strComment'], intval($sectionId), $_SESSION['osu']['username'], $_SESSION['osu']['id'], $_SESSION['osu']['avatar_url']));
        }

        echo json_encode("Success!");
        exit;
    }
}

if(isset($_POST['strUserID'])) {
    echo json_encode(getuser($_POST['strUserID']));
}

if(isset($_POST['nObject'])) {
    if(isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        $hasVoted = array();
        $hasVoted = Database::execSelect("SELECT Vote AS HasVoted FROM Votes Where UserID = ? AND ObjectID = ? AND Type = ? UNION SELECT 0 AS HasVoted LIMIT 1", "iii", array($_SESSION['osu']['id'], $_POST['nObject'], $_POST['nType']));

        if ($hasVoted[0]['HasVoted'] == 1) {
            Database::execOperation("DELETE FROM Votes WHERE UserID = ? AND ObjectID = ? AND Type = ?", "iii", array($_SESSION['osu']['id'], $_POST['nObject'], $_POST['nType']));
        } else {
            Database::execOperation("INSERT INTO Votes (UserID, ObjectID, Vote, Type) VALUES (?, ?, 1, ?)", "iii", array($_SESSION['osu']['id'], $_POST['nObject'], $_POST['nType']));
        }

        echo json_encode($hasVoted);
        exit;
    }
}

if(isset($_POST['nCommentDeletion'])) {
    if(isRestricted()) return;
    if(isset($_SESSION['osu']['id'])) {
        if($_SESSION['role']['rights'] > 0) {
            $comment_data = Database::execSelect("SELECT * FROM Comments WHERE ID = ?", "i", array($_POST['nCommentDeletion']))[0];
            $on = "unknown";
            if($comment_data['MedalID'] != null) {
                $on = "Medal " . Database::execSelect("SELECT name FROM Medals WHERE medalid = ?", "i", [$comment_data['MedalID']])[0]['name'];
            }
            if($comment_data['ProfileID'] != null) {
                $on = "User " . $comment_data['UserID'];
            }
            if($comment_data['VersionID'] != null) {
                $on = "Version " . Database::execSelect("SELECT Name FROM SnapshotsAzeliaVersions WHERE Id = ?", "i", [$comment_data['VersionID']])[0]['Name'];
            }
            Logging::PutLog("<h1>Deleted comment <strong>#{$_POST['nCommentDeletion']}</strong> by <strong>{$comment_data['Username']}</strong> on {$on}</h1><p>{$comment_data['PostText']}</p>");
            // END LOGGING
            Database::execOperation("DELETE FROM Comments WHERE ID = ?", "i", array($_POST['nCommentDeletion']));
        } else {
            Database::execOperation("DELETE FROM Comments WHERE ID = ? AND UserID = ?", "ii", array($_POST['nCommentDeletion'], $_SESSION['osu']['id']));
        }
        echo json_encode("Success!");
        exit;
    }
}
?>