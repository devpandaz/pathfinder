<?php include_once 'header.php'?>
<link rel="stylesheet" href="stylesheets/feedback.css">

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="form-wrapper">
        <div class="feedback-form">
            <div class="title-container">
                <h4>Feedback Form</h4>
            </div>
            <?php
                if (isset($_SESSION["give-feedback-error-empty"])) {
                    echo $_SESSION["give-feedback-error-empty"];
                    unset($_SESSION["give-feedback-error-empty"]);
                }
            ?>
            <form action="includes/feedback.inc.php" method="post">
                <fieldset class="form-field__name">
                    <legend>Name</legend>
                    <div class="form-field">
                        <?php
                            if (isset($_SESSION["give-feedback-error-username"])) {
                                echo $_SESSION["give-feedback-error-username"];
                                unset($_SESSION["give-feedback-error-username"]);
                            }
                        ?>
                        <input type="text" id="name" name="name" placeholder="Enter name">
                        <label for="name">First Name</label>
                    </div>

                    <div class="form-field">
                        <?php
                            if (isset($_SESSION["give-feedback-error-username"])) {
                                echo $_SESSION["give-feedback-error-username"];
                                unset($_SESSION["give-feedback-error-username"]);
                            }
                        ?>
                        <input type="text" id="name" name="name" placeholder="Enter name">
                        <label for="name">Last Name</label>
                    </div>
                </fieldset>

                <div class="form-field">
                    <?php
                        if (isset($_SESSION["give-feedback-error-email"])) {
                            echo $_SESSION["give-feedback-error-email"];
                            unset($_SESSION["give-feedback-error-email"]);
                        }
                    ?>
                    <label for="email">Email: </label>
                    <input type="email" id="email" name="email" placeholder="Enter email">
                </div>

                <div class="form-field">
                    <p>Please provide your feedback on the experience</p>
                    <div class="emoji-wrapper">
                        <label class="emoji-container">
                            <input type="radio" id="" class="sad" name="feedback" value="Bad">
                            <i class="far fa-frown"></i>
                        </label>
                        <label class="emoji-container">
                            <input type="radio" id="" class="average" name="feedback" value="Average">
                            <i class="far fa-meh"></i>
                        </label>
                        <label class="emoji-container">
                            <input type="radio" id="" class="happy" name="feedback" value="Good">
                            <i class="far fa-smile"></i>
                        </label>
                    </div>
                </div>
                
                <div class="form-field textarea-field">
                    <label for="comment">Do you have suggestions on what we can do to provide you with a better experience?</label>
                    <textarea name="comment" id="comment" cols="30" rows="8" style="resize: none;"></textarea>
                </div>
                <div class="submit-wrapper">
                    <button type="submit" class="form-submit" name="submit-feedback">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'footer.php'?>