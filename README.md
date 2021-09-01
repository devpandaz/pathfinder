**NOTE**: This library management system supposingly is made for our school, but since this the committee need to test and run this web app, the committee need to follow the steps below to simulate the same experience we have in our developers' environment. 

# Installation

**Download this repository by ```git clone https://github.com/jPRO-22/pathfinder.git``` or click "Download zip". Move the lms folder inside this pathfinder repository to your xampp htdocs folder and rename the lms folder to ```pathfinder```.**

1. Create a new Facebook page and setup the Facebook Chat Plugin. This is for the live chat features of this web app that the users can communicate with/seek live help from the authorities of the Facebook page. Once done setting up the chat plugin, change the code inside the line ```chatbox.setAttribute("page_id", "[code_here]");``` at [header.php](https://github.com/jPRO-22/pathfinder/blob/main/lms/header.php) (line 34) to your Facebook Chat Plugin code. 
2. Create a [Disqus](https://disqus.com/) account. Set up a new Disqus site. Once done, change s.src at [book.php](https://github.com/jPRO-22/pathfinder/blob/main/lms/book.php) (line 134: ```s.src = 'https://pathfinderlibms.disqus.com/embed.js';```) to the embed.js file of your Disqus site (you can get the embed source during setup/installation process of your Disqus site). 
3. Create a new Google Account specially for testing. Turn on "Allow less secure apps". Change the email at [includes/feedback.inc.php](https://github.com/jPRO-22/pathfinder/blob/main/lms/includes/feedback.inc.php) (line 54, 60, 61) and [includes/reset-request.inc.php](https://github.com/jPRO-22/pathfinder/blob/main/lms/includes/reset-request.inc.php) (line 65, 71) to the new Google account email created. 
4. Once all the accounts are set up, run the program on a server of your choice.
