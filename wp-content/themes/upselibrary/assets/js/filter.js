document.addEventListener('DOMContentLoaded', function () {
    const categoryFilter = document.getElementById('category-filter');
    const searchInput = document.getElementById('search-input');
    const postsGrid = document.getElementById('posts-grid');
    const pagination = document.getElementById('pagination');
    const postListTitle = document.getElementById('post-list-title');

    let currentPage = 1;

    const postsPerPage = {
        small: 6,  
        large: 8 
    };

    function getPostsPerPage() {
        return window.innerWidth >= 1025 ? postsPerPage.large : postsPerPage.small;
    }

    function getPosts(category = 'all', search = '', page = 1) {
        const postsPerPageCount = getPostsPerPage();
        const data = {
            action: 'filter_posts',
            category,
            search,
            page,
            posts_per_page: postsPerPageCount
        };

        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        })
            .then(res => res.json())
            .then(data => {
                renderPosts(data.posts);
                renderPagination(data.total_pages, page);
            });
    }

    function renderPosts(posts) {
        postsGrid.innerHTML = '';
        postsGrid.classList.add('posts-grid');

        const fragment = document.createDocumentFragment();
        posts.forEach(post => fragment.appendChild(createPostCard(post)));
        postsGrid.appendChild(fragment);
    }

    function createPostCard(post) {
        const postCard = document.createElement('a');
        postCard.className = 'post-card';
        postCard.href = post.link;
        postCard.innerHTML = `
            <img src="${post.thumbnail}" alt="${post.title}">
            <div class="post-content">
                <h3>${post.title}</h3>
                <p>${post.excerpt}</p>
            </div>
            <span class="read-more-button">Read More</span>
        `;
        return postCard;
    }

    function renderPagination(totalPages, currentPage) {
        pagination.innerHTML = '';
        const fragment = document.createDocumentFragment();

        if (currentPage > 1) {
            fragment.appendChild(createPaginationButton(1, 'First Page', 'ri-arrow-left-double-fill'));
            fragment.appendChild(createPaginationButton(currentPage - 1, 'Previous Page', 'ri-arrow-left-s-line'));
        }

        for (let i = 1; i <= totalPages; i++) {
            const isActive = i === currentPage ? 'active' : '';
            fragment.appendChild(createPaginationButton(i, `Page ${i}`, '', isActive));
        }

        if (currentPage < totalPages) {
            fragment.appendChild(createPaginationButton(currentPage + 1, 'Next Page', 'ri-arrow-right-s-line'));
            fragment.appendChild(createPaginationButton(totalPages, 'Last Page', 'ri-arrow-right-double-fill'));
        }

        pagination.appendChild(fragment);
    }

    function createPaginationButton(page, label, iconClass = '', activeClass = '') {
        const button = document.createElement('button');
        button.dataset.page = page;
        button.className = `page-btn ${activeClass}`;
        button.setAttribute('aria-label', label);
        button.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : page;
        return button;
    }

    function refreshPosts() {
        getPosts(categoryFilter.value, searchInput.value, currentPage);
    }

    categoryFilter.addEventListener('change', () => {
        currentPage = 1;
        refreshPosts();
    });

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        refreshPosts();
    });

    pagination.addEventListener('click', (e) => {
        const button = e.target.closest('button');
        if (!button?.dataset.page) return;

        currentPage = parseInt(button.dataset.page);
        refreshPosts();
        postListTitle?.scrollIntoView({ behavior: 'smooth' });
    });

    window.addEventListener('resize', debounce(() => {
        refreshPosts();
    }, 300));

    function debounce(func, delay) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Initial load
    getPosts();
});
