<div class="basic-page-content-padded basic-page-content-padded-scrollable">
    <h1>Upload new image</h1>
    <form action="/admin/panel/api/home/images/upload" method="post" enctype="multipart/form-data">
        <div class="images__image-preview">
            <img id="preview">
        </div>
        <p>Select image to upload:</p>
        <input type="file" name="file" id="file">
        <p>Caption</p>
        <input class="input" type="text" name="caption" id="caption" placeholder="nice stairs">
        <p>Author</p>
        <input class="input" type="text" name="author" id="author" value="<?php echo $_SESSION['osu']['username']; ?>">
        <br><br>
        <input class="button" type="submit" value="Upload Image" name="submit">
    </form>
</div>