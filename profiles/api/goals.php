<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/api_controller.php");

function get_goal($userId, $value, $type, $gamemode)
{
    return Database::execSelectFirstOrNull(
        "SELECT * FROM Goals WHERE UserID = ? AND Value = ? AND Type = ? and Gamemode = ?",
        "isss",
        [$userId, $value, $type, $gamemode]
    );    
}

define("VALUE_DECIMAL_REGEX", "/^\d+(?:\.\d{1,2})?$/");
define("VALUE_INT_REGEX", "/^\d+$/");
define("VALID_GAMEMODES", array("taiko", "mania", "osu", "fruits"));
define("TYPES_WITH_VALUE_DECIMAL", array("PP", "% Medals", "Level", "Ranked Score"));
define("VALID_TYPES", array("PP", "Rank", "Country Rank", "Medals", "% Medals", "SS Count", "Badges", "Level", "Ranked Score"));

class GoalsApiController extends ApiController
{
    protected function post(): Response {
        if (!isset($_SESSION['osu']['id']) || isRestricted())
            return new UnauthorizedJsonResponse();

        if(isset($_POST['Value'])) {
            $userId = $_SESSION['osu']['id'];
            $type = $_POST['Type'];

            if (!in_array($type, VALID_TYPES))
                return new BadRequestJsonResponse("Invalid Type");
            
            $gamemode = $_POST['Gamemode'];

            if (!in_array($gamemode, VALID_GAMEMODES))
                return new BadRequestJsonResponse("Invalid Gamemode");

            $value = $_POST['Value'];
            $can_have_decimal_value = in_array($type, TYPES_WITH_VALUE_DECIMAL);

            if (
                ($can_have_decimal_value && !preg_match(VALUE_DECIMAL_REGEX, $value)) || 
                (!$can_have_decimal_value && !preg_match(VALUE_INT_REGEX, $value))
            ) {
                return new BadRequestJsonResponse("Invalid Value format");
            }

            if (get_goal($userId, $value, $type, $gamemode) != null)
                return new BadRequestJsonResponse("Goal already exists");

            Database::execOperation(
                "INSERT INTO Goals (UserID, Value, Type, Gamemode, CreationDate) VALUES (?, ?, ?, ?, NOW())", 
                "isss", 
                array($_SESSION['osu']['id'], $value, $_POST['Type'], $_POST['Gamemode'])
            );

            return new JsonResponse("Success!");
        }

        if(isset($_POST['GoalID'])) {
            if (!filter_var($_POST['GoalID'], FILTER_VALIDATE_INT))
                return new BadRequestJsonResponse("Invalid GoalID");
            
            Database::execOperation(
                "DELETE FROM Goals WHERE ID = ? AND UserID = ?", 
                "ii", 
                array($_POST['GoalID'], $_SESSION['osu']['id'])
            );

            return new JsonResponse("Success!");
        }

        if(isset($_POST['ClaimID'])) {
            if (!filter_var($_POST['ClaimID'], FILTER_VALIDATE_INT))
                return new BadRequestJsonResponse("Invalid ClaimID");

            Database::execOperation(
                "UPDATE Goals SET Claimed = NOW() WHERE ID = ((? - Value) / 100) AND UserID = ?", 
                "ii", 
                array($_POST['ClaimID'], $_SESSION['osu']['id'])
            );
            return new JsonResponse("Success!");
        }

        return new NotImplementedResponse;
    }
}

(new GoalsApiController())->execute();

?>