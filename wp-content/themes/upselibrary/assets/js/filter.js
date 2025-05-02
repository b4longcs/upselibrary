document.addEventListener('DOMContentLoaded', function () {
    const categoryFilter = document.getElementById('category-filter');
    const searchInput = document.getElementById('search-input');
    const postsGrid = document.getElementById('posts-grid');
    const pagination = document.getElementById('pagination');

    let currentPage = 1;
    const postsPerPage = {
        desktop: 12,
        tablet: 9,
        mobile: 6
    };

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

    function renderPosts(posts) {
        postsGrid.innerHTML = '';
        posts.forEach(post => {
            const postCard = document.createElement('a'); // <--- use <a> as the outer wrapper
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
            postsGrid.appendChild(postCard);
        });
    }
      
    function renderPagination(totalPages, currentPage) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
    
        if (currentPage > 1) {
            pagination.innerHTML += `
                <button class="nav-btn first-page" data-page="1" aria-label="First Page">
                    <i class="ri-arrow-left-double-fill"></i>
                </button>`;
            pagination.innerHTML += `
                <button class="nav-btn prev-page" data-page="${currentPage - 1}" aria-label="Previous Page">
                    <i class="ri-arrow-left-s-line"></i>
                </button>`;
        }
    
        for (let i = 1; i <= totalPages; i++) {
            pagination.innerHTML += `
                <button 
                    data-page="${i}" 
                    class="page-btn ${i === currentPage ? 'active' : ''}" 
                    ${i === currentPage ? 'aria-current="page"' : ''}>
                    ${i}
                </button>`;
        }
    
        if (currentPage < totalPages) {
            pagination.innerHTML += `
                <button class="nav-btn next-page" data-page="${currentPage + 1}" aria-label="Next Page">
                    <i class="ri-arrow-right-s-line"></i>
                </button>`;
            pagination.innerHTML += `
                <button class="nav-btn last-page" data-page="${totalPages}" aria-label="Last Page">
                    <i class="ri-arrow-right-double-fill"></i>
                </button>`;
        }
    }
    
    function getPostsPerPage() {
        const width = window.innerWidth;
        if (width > 1024) return postsPerPage.desktop;
        if (width > 768) return postsPerPage.tablet;
        return postsPerPage.mobile;
    }

    categoryFilter.addEventListener('change', () => {
        getPosts(categoryFilter.value, searchInput.value, 1);
    });

    searchInput.addEventListener('input', () => {
        getPosts(categoryFilter.value, searchInput.value, 1);
    });

    pagination.addEventListener('click', (e) => {
        const button = e.target.closest('button');
        if (!button || !button.dataset.page) return;
    
        const page = parseInt(button.dataset.page);
        if (!isNaN(page)) {
            getPosts(categoryFilter.value, searchInput.value, page);
            document.getElementById('post-list-title')?.scrollIntoView({ behavior: 'smooth' });
        }
    });
    
    window.addEventListener('resize', () => {
        getPosts(categoryFilter.value, searchInput.value, currentPage);
    });

    getPosts();
});