const state = {
    searchKeys: [],
    value: '',
    orderBy: '',
    currentPage: 1,
    dataLimit: 2,
    baseURL: "includes/query_book.inc.php",

    url() {
        return this.baseURL+
        "?searchKeys="+JSON.stringify(this.searchKeys)+
        "&value="+this.value+
        "&orderBy="+this.orderBy+
        "&index="+this.currentPage+
        "&dR="+this.dataLimit;
    },
}


const pagination = {
    maxDisplay: 5,
    
    correctPage(current, pages) {
        if (pages == 1) {
            firstPage.value = prevPage.value = nextPage.value = lastPage.value = 1;                
        } else {
            if(current > 1) {
                firstPage.value = 1;
                prevPage.value = current - 1;
            } else {
                prevPage.value = 1;
            }
            if(current < pages) {
                nextPage.value = current + 1;
                lastPage.value = pages;
            } else {
                nextPage.value = pages;
            }
        }
    },

    border(current, pages, max) {
        let left = (current - Math.floor(max / 2));
        let right = (current + Math.floor(max / 2));

        if (left < 1) {
            left = 1;
            right = max;
        }
        if (right > pages) {
            left = pages - (max - 1);
            if (left < 1){
                left = 1;
            }
            right = pages;
        }
        return [left, right];
    },
    
    render(pages) {
        let maxLeft, maxRight, page;
        let currentPage = state.currentPage;
        
        this.correctPage(currentPage, pages);
        [maxLeft, maxRight] = this.border(currentPage, pages, this.maxDisplay); 

        wrapper.innerHTML = ``;
        for (page = maxLeft; page <= maxRight; page++) {
            if (page == currentPage){
                wrapper.innerHTML += `<button value=${page} class="page active">${page}</button>`
            } else {
                wrapper.innerHTML += `<button value=${page} class="page">${page}</button>`
            }
        }

        label.textContent=`${result.start}-${result.end} out of ${result.length} results `;

        Array.from(document.getElementsByClassName("page")).forEach((element) => {
            element.addEventListener("click", function() {
                skipToPage(element.value);
            });
        });
    },
}