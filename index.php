<?php include_once 'header.php'?>
    <div class="page-wrapper">
    <?php
        if (isset($_SESSION["userusername"])) {
            echo "<p>Hello there" . $_SESSION["userusername"] . "</p>";
        }
    ?>

    <link rel="stylesheet" href="stylesheet/homepage.css" />
    <section class="content featured-book">
        <div class="left box">
            <h2>Featured Books</h2>
            <div>
                Find and explore your new favourite books handpicked by our staff. Our collection is wide and we add on to it very regularly. <br>
                <a href="something">Click Here</a> to see a list of all our new book collection. <br>
                <a href="something">Click Here</a> to see more of our featured eBook collections.
            </div>
        </div>
        <div class="right box">
        </div>
        


    </section>
        
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
<?php include_once 'footer.php'?>