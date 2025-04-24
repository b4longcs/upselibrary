document.addEventListener('DOMContentLoaded', () => {
    const carouselImages = document.querySelector('.carousel-images');
    const carouselPagination = document.querySelector('.carousel-pagination');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');

    async function fetchRecentPosts() {
        const response = await fetch('/wp-json/wp/v2/posts?per_page=5&_embed');
        const posts = await response.json();
        return posts;
    }

    function createCarousel(posts) {
        carouselImages.innerHTML = '';
        carouselPagination.innerHTML = '';

        posts.forEach((post, index) => {
            const featuredImage = post._embedded['wp:featuredmedia']?.[0]?.source_url || '';
            const postTitle = post.title.rendered;
            const postLink = post.link;

            const imgElement = document.createElement('img');
            imgElement.src = featuredImage;
            imgElement.alt = postTitle;
            imgElement.dataset.index = index;
            imgElement.onclick = () => window.location.href = postLink;
            carouselImages.appendChild(imgElement);

            const paginationButton = document.createElement('button');
            paginationButton.dataset.index = index;
            paginationButton.onclick = () => goToSlide(index);

            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            paginationButton.appendChild(progressBar);
            carouselPagination.appendChild(paginationButton);
        });

        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-images img');
        const paginationButtons = document.querySelectorAll('.carousel-pagination button');

        function showSlide(index) {
            const slideWidth = carouselImages.clientWidth;
            carouselImages.style.transform = `translateX(-${index * slideWidth}px)`;
            paginationButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.querySelector('.progress-bar').style.width = '0%';
            });
            paginationButtons[index].classList.add('active');
            const progressBar = paginationButtons[index].querySelector('.progress-bar');
            progressBar.style.width = '0%';
            setTimeout(() => (progressBar.style.width = '100%'), 10);
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(index);
            resetAutoplay();
        }

        let autoPlayInterval;
        let autoPlayTimeout;

        function startAutoPlay() {
            autoPlayInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }, 4000);
        }

        function stopAutoPlay() {
            clearInterval(autoPlayInterval);
        }

        function resetAutoplay() {
            stopAutoPlay();
            clearTimeout(autoPlayTimeout);
            autoPlayTimeout = setTimeout(() => {
                startAutoPlay();
            }, 2000);
        }

        showSlide(currentSlide);
        startAutoPlay();

        carouselImages.addEventListener('mouseenter', stopAutoPlay);
        carouselImages.addEventListener('mouseleave', resetAutoplay);

        prevButton.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
            resetAutoplay();
        });

        nextButton.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
            resetAutoplay();
        });

        let startX, isDragging = false;

        function onDragStart(e) {
            startX = e.clientX || e.touches[0].clientX;
            isDragging = true;
            carouselImages.classList.add('dragging');
        }

        function onDragMove(e) {
            if (!isDragging) return;
            const currentX = e.clientX || e.touches[0].clientX;
            const deltaX = startX - currentX;
            carouselImages.style.transform = `translateX(${-currentSlide * carouselImages.clientWidth - deltaX}px)`;
        }

        function onDragEnd(e) {
            if (!isDragging) return;
            const endX = e.clientX || e.changedTouches[0].clientX;
            const deltaX = startX - endX;
            if (Math.abs(deltaX) > 50) {
                currentSlide = deltaX > 0
                    ? (currentSlide + 1) % slides.length
                    : (currentSlide - 1 + slides.length) % slides.length;
            }
            showSlide(currentSlide);
            isDragging = false;
            carouselImages.classList.remove('dragging');
        }

        carouselImages.addEventListener('mousedown', onDragStart);
        carouselImages.addEventListener('mousemove', onDragMove);
        carouselImages.addEventListener('mouseup', onDragEnd);
        carouselImages.addEventListener('mouseleave', onDragEnd);
        carouselImages.addEventListener('touchstart', onDragStart);
        carouselImages.addEventListener('touchmove', onDragMove);
        carouselImages.addEventListener('touchend', onDragEnd);

        window.addEventListener('resize', () => showSlide(currentSlide));
    }

    fetchRecentPosts().then(createCarousel);
});
