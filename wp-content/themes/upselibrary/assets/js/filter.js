document.addEventListener('DOMContentLoaded', () => {
    // DOM elements
    const categoryFilter = document.getElementById('category-filter');
    const searchInput = document.getElementById('search-input');
    const postsGrid = document.getElementById('posts-grid');
    const pagination = document.getElementById('pagination');
    const postListTitle = document.getElementById('post-list-title');

    // State
    let currentPage = 1;
    const postsPerPage = { small: 6, large: 8 };

    // Get posts per page based on screen size
    const getPostsPerPage = () => window.innerWidth >= 1025 ? postsPerPage.large : postsPerPage.small;

    // Fetch and render posts based on filters and pagination
    const getPosts = (category = 'all', search = '', page = 1) => {
        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'filter_posts',
                category,
                search,
                page,
                posts_per_page: getPostsPerPage()
            })
        })
        .then(res => res.json())
        .then(data => {
            renderPosts(data.posts);
            renderPagination(data.total_pages, page);
        });
    };

    // Render posts in the grid
    const renderPosts = posts => {
        postsGrid.innerHTML = '';
        postsGrid.classList.add('posts-grid');
        const fragment = document.createDocumentFragment();
        posts.forEach(post => fragment.appendChild(createPostCard(post)));
        postsGrid.appendChild(fragment);
    };

    // Create single post card element
    const createPostCard = post => {
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
    };

    // Render pagination buttons
    const renderPagination = (totalPages, currentPage) => {
        pagination.innerHTML = '';
        const fragment = document.createDocumentFragment();

        // Previous button
        if (currentPage > 1) fragment.appendChild(createPaginationButton(currentPage - 1, 'Previous Page', '<'));

        // Page range: two before, current, two after
        const pages = getPageRange(currentPage, totalPages);
        pages.forEach(page => {
            const element = page === currentPage
                ? createCurrentPageSpan(page)
                : createPaginationButton(page, `Page ${page}`);
            fragment.appendChild(element);
        });

        // Next button
        if (currentPage < totalPages) fragment.appendChild(createPaginationButton(currentPage + 1, 'Next Page', '>'));

        pagination.appendChild(fragment);
    };

    // Generate a range of pages for pagination
    const getPageRange = (current, total) => {
        const pages = [];
        for (let i = current - 2; i <= current + 2; i++) {
            if (i > 0 && i <= total) pages.push(i);
        }
        return pages;
    };

    // Create pagination button
    const createPaginationButton = (page, label, text = '') => {
        const button = document.createElement('button');
        button.dataset.page = page;
        button.className = 'page-btn';
        button.setAttribute('aria-label', label);
        button.textContent = text || page;
        return button;
    };

    // Create span for current page
    const createCurrentPageSpan = page => {
        const span = document.createElement('span');
        span.className = 'current-page';
        span.textContent = page;
        return span;
    };

    // Refresh posts based on filters and page
    const refreshPosts = () => getPosts(categoryFilter.value, searchInput.value, currentPage);

    // Debounce utility
    const debounce = (func, delay) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    };

    // Event: Category change
    categoryFilter.addEventListener('change', () => {
        currentPage = 1;
        refreshPosts();
    });

    // Event: Search input
    searchInput.addEventListener('input', () => {
        currentPage = 1;
        refreshPosts();
    });

    // Event: Pagination click
    pagination.addEventListener('click', e => {
        const button = e.target.closest('button');
        if (!button?.dataset.page) return;
        currentPage = parseInt(button.dataset.page);
        refreshPosts();
        postListTitle?.scrollIntoView({ behavior: 'smooth' });
    });

    // Event: Window resize
    window.addEventListener('resize', debounce(refreshPosts, 300));

    // Initial load
    getPosts();
});
