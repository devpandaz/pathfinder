<?php require "header.php"?>
<style>
    .flex-vertical{
        display: flex;
        flex-direction: column;
    }
    .main-content {
        padding: 0 50px;
    }
    .form-field {
        margin: 10px 0;
    }
</style>

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>

    <div class="main-content">
        <form method="post" action="includes/notify.inc.php">
            <div class="form-field">
                <label for="username">Username: </label>
                <input type="text" name="username">
            </div>
            <div class="form-field flex-vertical">
                <label for="notify-content">Notification Content: </label>
                <textarea name="notify-content" cols="30" rows="3" style="resize: none;" placeholder="Content..."></textarea>
            </div>
            <button type="submit">Send</button>
        </form>
    </div>
</div>


<?php include_once 'footer.php';?>