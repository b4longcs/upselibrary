document.addEventListener('DOMContentLoaded', function () {
    // Cache DOM elements
    const categoryFilter = document.getElementById('category-filter');
    const searchInput = document.getElementById('search-input');
    const postsGrid = document.getElementById('posts-grid');
    const pagination = document.getElementById('pagination');
    const postListTitle = document.getElementById('post-list-title');

    let currentPage = 1;
    const postsPerPage = {
        desktop: 12,
        tablet: 9,
        mobile: 6
    };

    // Function to get posts via AJAX
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
            .then(response => response.json())
            .then(data => {
                renderPosts(data.posts);
                renderPagination(data.total_pages, page);
            });
    }

    // Function to render posts
    function renderPosts(posts) {
        postsGrid.innerHTML = ''; // Clear previous posts

        // Add 'posts-grid' class only for the CSA page
        if (!postsGrid.classList.contains('posts-grid')) {
            postsGrid.classList.add('posts-grid');
        }

        // Create and append each post card
        const fragment = document.createDocumentFragment(); // Create a document fragment to minimize DOM reflows
        posts.forEach(post => {
            const postCard = createPostCard(post);
            fragment.appendChild(postCard);
        });

        postsGrid.appendChild(fragment);
    }

    // Function to create a post card element
    function createPostCard(post) {
        const postCard = document.createElement('a');
        postCard.classList.add('post-card');
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

    // Function to render pagination
    function renderPagination(totalPages, currentPage) {
        const fragment = document.createDocumentFragment();
        pagination.innerHTML = ''; // Clear pagination

        // Handle previous and first page buttons
        if (currentPage > 1) {
            fragment.appendChild(createPaginationButton(1, 'First Page', 'ri-arrow-left-double-fill'));
            fragment.appendChild(createPaginationButton(currentPage - 1, 'Previous Page', 'ri-arrow-left-s-line'));
        }

        // Handle page buttons
        for (let i = 1; i <= totalPages; i++) {
            fragment.appendChild(createPaginationButton(i, `Page ${i}`, '', i === currentPage ? 'active' : ''));
        }

        // Handle next and last page buttons
        if (currentPage < totalPages) {
            fragment.appendChild(createPaginationButton(currentPage + 1, 'Next Page', 'ri-arrow-right-s-line'));
            fragment.appendChild(createPaginationButton(totalPages, 'Last Page', 'ri-arrow-right-double-fill'));
        }

        pagination.appendChild(fragment);
    }

    // Function to create a pagination button
    function createPaginationButton(page, label, iconClass, activeClass = '') {
        const button = document.createElement('button');
        button.dataset.page = page;
        button.classList.add('page-btn', activeClass);
        if (iconClass) {
            button.innerHTML = `<i class="${iconClass}"></i>`;
        } else {
            button.textContent = page;
        }
        button.setAttribute('aria-label', label);
        return button;
    }

    // Function to determine posts per page based on screen size
    function getPostsPerPage() {
        const width = window.innerWidth;
        if (width > 1024) return postsPerPage.desktop;
        if (width > 768) return postsPerPage.tablet;
        return postsPerPage.mobile;
    }

    // Event listeners for filter changes and search
    categoryFilter.addEventListener('change', () => {
        currentPage = 1; // Reset to the first page on category change
        getPosts(categoryFilter.value, searchInput.value, currentPage);
    });

    searchInput.addEventListener('input', () => {
        currentPage = 1; // Reset to the first page on search input
        getPosts(categoryFilter.value, searchInput.value, currentPage);
    });

    // Event listener for pagination buttons
    pagination.addEventListener('click', (e) => {
        const button = e.target.closest('button');
        if (!button || !button.dataset.page) return;

        currentPage = parseInt(button.dataset.page);
        if (!isNaN(currentPage)) {
            getPosts(categoryFilter.value, searchInput.value, currentPage);
            postListTitle?.scrollIntoView({ behavior: 'smooth' }); // Scroll to the post list title
        }
    });

    // Event listener for window resizing
    window.addEventListener('resize', () => {
        getPosts(categoryFilter.value, searchInput.value, currentPage); // Re-fetch posts on resize
    });

    // Initial post fetch
    getPosts();
});
