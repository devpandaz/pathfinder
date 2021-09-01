<?php
    session_start(); 
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PathFinder</title>
    <link rel="apple-touch-icon" sizes="180x180" href="icons/logo/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/logo/favicon-16x16.png">
    <link rel="manifest" href="icons/logo/site.webmanifest">
    <link rel="stylesheet" href="stylesheets/header.css" />
    <link rel="stylesheet" href="stylesheets/notification.css" />
    <link rel="stylesheet" href="stylesheets/sidebar.css">
    <link rel="stylesheet" href="stylesheets/footer.css" />
    <link rel="stylesheet" href="stylesheets/animation/hamburger.css" />
    <link rel="stylesheet" href="perfect-scrollbar-master/css/perfect-scrollbar.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- Messenger Chat plugin Code -->
    <div id="fb-root"></div>

    <!-- Your Chat plugin code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "102055062196215");
      chatbox.setAttribute("attribution", "biz_inbox");

      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v11.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <header>
        <div class="header-container">
            <div class="left">
                <button type="button" class="hamburger hamburger--elastic">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
                <div class="logo-container">
                    <img src="images/librarian-logo.jpg" alt="logo">
                    <h3>Yok Bin Library</h3>
                </div>
                
            </div>
            <nav class="header-nav">
                <div class="menu">
                    <span>
                        <a href="#" class="btn-icon">
                            <span class="btn-icon-wrapper">
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                        </a>
                    </span>
                </div>

                <ul class="menu-content">
                    
                    <li><a href="index.php">Home</a></li>
                    <li><a href="books.php">Books</a></li>

                    <?php
                        if (isset($_SESSION["userid"])) {
                            echo "<li><a href='profile.php'>Profile</a></li>";
                            echo "<li><a href='includes/logout.inc.php'>Log Out</a></li>";
                            echo "<li class='notification'>
                                <a href='#' onclick='return false'>
                                    <div class='notBtn' href='#' onclick='return false'>
                                        <div class='number'></div>
                                        <i class='fas fa-bell'></i>
                                        <div class='box'>
                                            <div class='display'>
                                                <div class='nothing'> 
                                                    <i class='fas fa-child stick'></i> 
                                                    <div class='cent'>Looks Like your all caught up!</div>
                                                </div>
                                                <div class='cont'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>";
                        } else {
                            echo "<li><a href='entry/signup.php'>Sign up</a></li>";
                            echo "<li><a href='entry/login.php'>Log In</a></li>";
                        }
                    ?>
                </ul>
            </nav>
        </div>
    </header>

    <?php if(isset($_SESSION["userid"])) {
        echo '<script>
        const user = '. $_SESSION["userid"] .';

        function setFlagRead(notificationId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "includes/read-notification.inc.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("notificationId=" + notificationId);
        }

        function getNotification() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "includes/get-notification.inc.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {  
                if(this.status === 200 ) {  
                    let response = this.responseText;  
                    let notifications = JSON.parse( response ); // create JSON object from response 
                    const container = document.getElementsByClassName("cont")[0];
                    const nothing = document.getElementsByClassName("nothing")[0];

                    let i, row, num = 0;
                    if(notifications.length) {
                        nothing.style.display = "none";
                    }
                    for (i = 0; i < notifications.length; i++){  
                        let notificationId = notifications[i].id;
                        let time = notifications[i].timestamp;
                        let text = notifications[i].text;
                        let read = notifications[i].read;

                        if (typeof read == "string") {
                            read = parseInt(read)
                        }

                        let className = "sec";
                        if(!read) {
                            num++;
                            className += " new"
                        }

                        row = `
                            <div id="${notificationId}" class="${className}">
                                <div class="txt">${text}</div>
                                <div class="sub">${time}</div>
                            </div>
                        `

                        container.innerHTML = row + container.innerHTML;
                        
                        // add notification into your HTML here  
                    }
                    if(num) {
                        document.getElementsByClassName("number")[0].textContent = num
                    }

                    Array.from(document.getElementsByClassName("sec")).forEach(element => {
                        element.addEventListener("click", function() {
                            if(element.classList.contains("new")) {
                                element.classList.remove("new");
                                if(--num) {
                                    document.getElementsByClassName("number")[0].textContent = num;
                                } else {
                                    document.getElementsByClassName("number")[0].textContent = "";
                                }
                                setFlagRead(element.id);
                            }
                        });
                    });
                }  
            }  
            xhr.send("user="+user);  
        }
        getNotification();
    </script>';
    }?>
    
    
    
