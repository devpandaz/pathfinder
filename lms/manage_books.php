<?php include_once 'header.php'?>

    <link rel="stylesheet" href="stylesheets/search_book.css">
    <link rel="stylesheet" href="stylesheets/animation/loading.css">
    <link rel="stylesheet" href="stylesheets/alert.css">
    
	<div class="page-wrapper">
        <?php include_once 'sidebar.php'?>
        <div class="main-content">
            <div class="form-wrapper">
                <form id="search" action="">
                    <div class="form-inner">
                        <div class="form-field field__option">
                            <h4>Search By:</h4>
                            <input type="checkbox" id="options" name="options" value="all">
                            <label for="options"> Search All</label><br>
                            

                            <input type="checkbox" class="switch" id="option1" name="option1" value="booksTitle">
                            <label for="option1"> Book Title</label><br>

                            <input type="checkbox" class="switch" id="option2" name="option2" value="booksISBN">
                            <label for="option2"> ISBN</label><br>
                            
                            <input type="checkbox" class="switch" id="option3" name="option3" value="booksAuthor">
                            <label for="option3"> Author</label><br>

                            <input type="checkbox" class="switch" id="option4" name="option4" value="booksCategory">
                            <label for="option4"> Category</label><br>

                            <input type="checkbox" class="switch" id="option5" name="option5" value="booksYear">
                            <label for="option5"> Published Year</label><br>

                            <input type="checkbox" class="switch" id="option6" name="option6" value="booksPublisher">
                            <label for="option6"> Publisher</label><br>

                            <input type="checkbox" class="switch" id="option7" name="option7" value="booksShelf">
                            <label for="option7"> Shelf</label><br>

                            <input type="checkbox" class="switch" id="option8" name="option8" value="booksLanguage">
                            <label for="option8"> Language</label><br>
                            <small class="respond" id="rsp_option"></small>
                        </div>

                        <div class="form-field">
                            <i class="fas fa-search"></i>
                            <label for="searchValue"></label>
                            <input type="text" id="searchValue" name="searchValue" placeholder="Enter Keyword" value="<?php if(isset($_GET["searchValue"])) {echo $_GET["searchValue"];}?>">
                            <small class="respond" id="rsp_searchValue"></small>
                            <button type="submit">Search</button>
                        </div>

                        <div class="form-field">
                            <label for="order">Order By:</label>
                            <select name="order" id="order">
                                <option value="booksTitle">Title</option>
                                <option value="booksYear">Published Year</option>
                                <option value="booksCategory">Category</option>
                                <option value="booksAuthor">Author</option>
                                <option value="booksPublisher">Publisher</option>
                                <option value="booksShelf">Shelf</option>
                                <option value="booksLanguage">Language</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="msg">
                
            </div>

            <!-- notices -->
            <?php
                if (isset($_SESSION["delete-book-success-msg"])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION["delete-book-success-msg"] . '</div>';
                }
                unset($_SESSION['delete-book-success-msg']);

                if (isset($_SESSION["book-not-deleted"])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION["book-not-deleted"] . '</div>';
                }
                unset($_SESSION['book-not-deleted']);
            ?>

            <div class="result-container">
                <ol class="result-inner">
            
                <ol>			
            </div>

            <div class="pagination">
                <nav class="pagination-inner">
                    <a href="#" id="first" class="button">
                        <span><<</span>
                    </a>
                    <a href="#" id="previous" class="button">
                        <span><</span>
                    </a>
                    <div id="btn-wrapper">

                    </div>
                    <a href="#" id="next" class="button">
                        <span>></span>
                    </a>
                    <a href="#" id="last" class="button">
                        <span>>></span>
                    </a>
                </nav>
                <div class="form-wrapper">
                    <form id="page-skip" action="">
                        <input name ="index" id="index" type="text" placeholder="Enter page index">
                        <label id="lbl-index" for="index"></label>
                        <small class="respond" id="rsp_index"></small>
                    </form>
                </div>
            </div>
        </div>  
    </div>

    <script>
        const searchForm = document.getElementById("search");
        const checkAll = document.getElementById("options");
        const checkboxes = Array.from(document.getElementsByClassName("switch"));

        const output = document.querySelector(".result-inner");

        const wrapper = document.getElementById('btn-wrapper');
        const firstPage = document.getElementById('first');
        const prevPage = document.getElementById('previous');
        const nextPage = document.getElementById('next');
        const lastPage = document.getElementById('last');
        
        const indexForm = document.getElementById("page-skip");
        const label = document.getElementById("lbl-index");

        const message = document.getElementsByClassName("msg")[0];
        const resultContainer = document.getElementsByClassName("result-container")[0];
        const paginationContainer = document.getElementsByClassName("pagination")[0];

        const state = {
            searchKeys: [],
            value: '',
            orderBy: '',
            currentPage: 1,
            dataLimit: 10,
            baseURL: "includes/query_book.inc.php",
            url() {
                return this.baseURL+
                "?searchKeys="+JSON.stringify(this.searchKeys)+
                "&value="+this.value+
                "&orderBy="+this.orderBy+
                "&index="+this.currentPage+
                "&dR="+this.dataLimit;
            }
		}

        const result = {
            length: 0,
            start: 0,
            end: 0,
            list: []
        }

        function retrieve() {
            let keys = [];
            let value = this.searchValue.value;
            let order = this.order.value;

            checkboxes.forEach((el) => {
                if(el.checked) {
                    keys.push(el.value);
                }
            });

            const rspEl = document.getElementById("rsp_option");
            if (!keys.length) {
                rspEl.textContent = "Must have a search range!";
                return false;
            }
            rspEl.textContent = "";

            state.searchKeys = keys;
            state.value = value;
            state.currentPage = 1;
            state.orderBy = order;
            return true;
        }

        const ajaxRequest = () => {
            resultContainer.style.display = "block";
            paginationContainer.style.display = "none";
            message.style.marginTop = "0";
            message.innerHTML = "";
            output.innerHTML = `
                            <div class="lds-container">
                                <div class="lds-ring">
                                    <div></div><div></div><div></div><div></div>
                                </div>
                            </div>
                            `;

            const xhr = new XMLHttpRequest();
            xhr.onload = function() {
                if(this.status == 200){
                    [result.length, result.start, result.end, result.list] = JSON.parse(this.responseText);
                    show(output);
                }
            }
            xhr.open("GET", state.url(), true);
            xhr.send();
        }

        const pageCount = (length) => {
            return Math.ceil(length / state.dataLimit) || 1;
        }

        function show(element) {
            element.innerHTML = '';
            if(!result.length) {
                resultContainer.style.display = "none";
                paginationContainer.style.display = "none";
                message.style.marginTop = "100px";
                message.innerHTML = `
                                        <h1> No Books Were Found </h1>
                `
                return false;
            }
            paginationContainer.style.display = "block";

            let pages = pageCount(result.length);
            let list = result.list;
            let i;

            for (i = 0; i < list.length; i++) {
                if (list[i].bookCover == "default-book-cover.png") {
                    image = "images/default-book-cover.png";
                } else {
                    image = "uploads/book-covers/" + list[i].bookCover;
                }
                let row = `
                        <li class="list-item">
                            <div class="book-cover-container">
                                <div class="book-cover-wrapper">
                                <img class="book-cover" src="${image}" alt="book cover"/>
                                </div>
                            </div>
                            <section class="content-preview">
                                <div class="header">
                                    <h3><a href="book.php?id=${list[i].isbn}">${list[i].title}</a></h3>
                                </div>
                                <span>ISBN: ${list[i].isbn}</span>
                                <span><span class="checkmark"></span> In stock</span>
                                <b class="desc-header">Description</b>
                                <button class="show-desc"> Show Description </button>
                                <div class="desc">${list[i].detail}</div>
                                <div class="controller">
                                    <a href="edit_book.php?id=${list[i].isbn}">Edit</a>
                                    <a href="includes/delete-book.inc.php?id=${list[i].isbn}" onclick="return confirm('Are you sure you want to delete the book named &quot;${list[i].title}&quot;?')">Delete</a>
                                </div>
                            </section>
                        </li>
                        `
                element.innerHTML += row;
            }
            Array.from(document.getElementsByClassName("show-desc")).forEach((el) => {
                el.addEventListener("click", function() {
                    event.preventDefault();
                    const target = event.currentTarget;
                    accordion(target, false);
                });
            });
            pagination(pages);
        }

        const jumpToPage = (index) => {
            if (typeof index == "string") {
                index = parseInt(index)
            }

            if(state.currentPage == index){
                return false;
            }
            state.currentPage = index;
            ajaxRequest();
        };        

        function pagination(pages) {
            const maxPage = 3;
            let current = state.currentPage;

            firstPage.value = 1;
            lastPage.value = pages;

            if (pages == 1) {
                prevPage.value = nextPage.value = 1;
            } else {
                if(current > 1) {
                    prevPage.value = current - 1;
                } else {
                    prevPage.value = 1;
                }

                if(current < pages) {
                    nextPage.value = current + 1;
                } else {
                    nextPage.value = pages;
                }
            }
            
            let maxLeft = (current - Math.floor(maxPage / 2));
            let maxRight = (current + Math.floor(maxPage / 2));

            if (maxLeft < 1) {
                maxLeft = 1;
                maxRight = maxPage;
            }

            if (maxRight > pages) {
                maxLeft = pages - (maxPage - 1);
                if (maxLeft < 1){
                    maxLeft = 1;
                }
                maxRight = pages;
            }

            wrapper.innerHTML = ``;
            let page;
            for (page = maxLeft; page <= maxRight; page++) {
                if (page == current){
                    wrapper.innerHTML += `<a class="page active">${page}</a>`
                } else {
                    wrapper.innerHTML += `<a class="page">${page}</a>`
                }
            }
            
            let range = result.start == result.end ? result.start : result.start + " - " + result.end;
            label.textContent = `${range} out of ${result.length} results`;

            Array.from(document.getElementsByClassName("page")).forEach((el) => {
                el.addEventListener("click", function(event) {
                    event.preventDefault();
                    jumpToPage(el.textContent);
                });
            });
        }

        window.addEventListener("load", function() {
            checkAll.click();

            if (retrieve()) {
                ajaxRequest();
            }
        });

        searchForm.addEventListener("submit", function(event){
            event.preventDefault();

            if (retrieve()) {
                ajaxRequest();
            }
        });

        checkAll.addEventListener("click", function() {
            let bool = this.checked;
            checkboxes.forEach((el) => {
                el.checked = bool;
            });
        });

        checkboxes.forEach((el) => {
            el.addEventListener("click", function() {
                if(!this.checked) {
                    checkAll.checked = false;
                } else {
                    let turnOn = true;
                    checkboxes.forEach((el) => {
                        turnOn = turnOn && el.checked;
                    });
                    checkAll.checked = turnOn;
                }
            });
        });

        [firstPage, prevPage, nextPage, lastPage].forEach((el) => {
            el.addEventListener('click', function(event){
                event.preventDefault();
                let value = el.value;
                if (!value) {
                    return false;
                }
                jumpToPage(value);
            });
        });

        indexForm.addEventListener("submit", function(){
            event.preventDefault();

            let page = this.index.value;

            const rspEl = document.getElementById("rsp_index");
            if (page < 1 || page > pageCount(result.length)) {
                rspEl.textContent = "Out of range!";
                return false;
            }
            rspEl.textContent = "";
            jumpToPage(page);
        });
    </script>
<?php include_once 'footer.php'?>