document.addEventListener('DOMContentLoaded', () => {
  Header.init();
  Tabs.init();
  ScrollReveal.init();
  AjaxPosts.init();
  Global.init();
  NewAcquisitionToggle.init();
  setupPreloader();
});

// ====================================
// MODULE: Header
// ====================================
const Header = (() => {
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('navLinks');
  const closeBtn = document.getElementById('close-btn');
  const overlay = document.getElementById('overlay');
  const navMenus = document.querySelectorAll('.nav-links > li');
  const headerSticky = document.querySelector('.header-sticky');
  let lastScrollTop = 0;

  const toggleSidebar = (open = true) => {
    navLinks.classList.toggle('active', open);
    overlay.classList.toggle('active', open);
    if (!open) {
      navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
    }
  };

  const initSidebar = () => {
    hamburger?.addEventListener('click', () => toggleSidebar(true));
    [closeBtn, overlay].forEach(el => el?.addEventListener('click', () => toggleSidebar(false)));

    let currentlyOpen = null;

    navMenus.forEach(item => {
      const toggleLink = item.querySelector('.nav-menu');
      const subMenu = item.querySelector('.sub-menu');
      if (!toggleLink || !subMenu) return;

      toggleLink.addEventListener('click', e => {
        e.preventDefault();
        const isSameMenu = currentlyOpen === item;

        navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));

        if (!isSameMenu) {
          item.classList.add('open', 'active-indicator');
          currentlyOpen = item;
        } else {
          currentlyOpen = null;
        }
      });
    });
  };

  const handleScroll = () => {
    const currentScroll = window.scrollY;
    if (currentScroll < window.innerWidth * 0.1) {
      headerSticky?.classList.remove('header-hidden');
    } else if (headerSticky) {
      headerSticky.classList.toggle('header-hidden', currentScroll > lastScrollTop);
    }
    lastScrollTop = Math.max(currentScroll, 0);
  };

  const setProgressBar = () => {
    const totalHeight = document.body.clientHeight;
    const ratio = ((window.innerHeight + window.scrollY) * 100) / totalHeight;
    document.body.style.setProperty('--progress-bar-width', `${ratio}%`);
  };

  const init = () => {
    initSidebar();

    document.querySelectorAll('.sub-menu a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        overlay.classList.remove('active');
      });
    });

    window.addEventListener('scroll', handleScroll);
    ['scroll', 'resize'].forEach(evt => window.addEventListener(evt, setProgressBar));
    setProgressBar();
  };

  return { init };
})();


// ====================================
// MODULE: Tabs
// ====================================
const Tabs = (() => {
  const init = () => {
    const tabList = document.querySelector('.tabs-nav');
    if (!tabList) return;

    const tabs = Array.from(tabList.querySelectorAll('.tab-button'));
    const panels = Array.from(document.querySelectorAll('.tab-panel'));
    const indicator = document.querySelector('.tabs-indicator');
    if (!indicator || tabs.length === 0) return;

    const setIndicatorPosition = tab => {
      indicator.style.transform = `translateX(${tab.offsetLeft}px)`;
      indicator.style.width = `${tab.offsetWidth}px`;
    };

    const switchTab = tab => {
      tabs.forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });

      panels.forEach(p => p.setAttribute('aria-hidden', 'true'));

      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');

      const targetPanel = document.getElementById(tab.getAttribute('aria-controls'));
      if (targetPanel) targetPanel.setAttribute('aria-hidden', 'false');

      setIndicatorPosition(tab);
    };

    // Initialize first tab and panel
    switchTab(tabs[0]);

    tabs.forEach(tab => tab.addEventListener('click', e => switchTab(e.currentTarget)));

    tabList.addEventListener('keydown', e => {
      const currentTab = document.activeElement;
      if (!tabs.includes(currentTab)) return;

      let newIndex = tabs.indexOf(currentTab);
      if (e.key === 'ArrowLeft') newIndex--;
      else if (e.key === 'ArrowRight') newIndex++;

      if (newIndex >= 0 && newIndex < tabs.length) {
        tabs[newIndex].click();
        tabs[newIndex].focus();
      }
    });
  };

  return { init };
  
  document.querySelectorAll('.search-container').forEach(form => {
    form.addEventListener('submit', function (e) {

        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        if (isSafari) {
            e.preventDefault();
            const url = new URL(form.action);
            const input = form.querySelector('input');
            if (input && input.name && input.value) {
                url.searchParams.set(input.name, input.value);
                window.open(url.toString(), '_blank');
            }
        }
    });
  });
  
})();

// ====================================
// MODULE: ScrollReveal
// ====================================
const ScrollReveal = (() => {
  const getViewportHeight = () => Math.max(document.documentElement.clientHeight, window.innerHeight);

  const getOffset = el => {
    let top = 0;
    while (el) {
      top += el.offsetTop || 0;
      el = el.offsetParent;
    }
    return top;
  };

  const isInViewport = (el, threshold = 0.33) => {
    const scroll = window.pageYOffset;
    const view = scroll + getViewportHeight();
    const top = getOffset(el);
    const bottom = top + el.offsetHeight;
    return top + el.offsetHeight * threshold <= view && bottom >= scroll;
  };

  const parseData = el => {
    const tokens = el.getAttribute('data-scrollreveal')
      .split(/[, ]+/)
      .filter(w => !['from', 'the', 'and', 'then', 'but'].includes(w));
    const obj = {};

    tokens.forEach((t, i) => {
      if (t === 'enter') obj.from = tokens[i + 1];
      if (t === 'wait' || t === 'after') obj.delay = tokens[i + 1];
      if (t === 'move') obj.distance = tokens[i + 1];
      if (t === 'over') obj.duration = tokens[i + 1];
    });

    obj.axis = ['top', 'bottom'].includes(obj.from) ? 'y' : 'x';
    if (['top', 'left'].includes(obj.from)) obj.distance = `-${obj.distance || '25px'}`;

    return obj;
  };

  const getCSS = el => {
    const d = parseData(el);
    const duration = d.duration || '0.66s';
    const delay = d.delay || '0s';
    const dist = d.distance || '25px';
    const axis = d.axis || 'y';

    return {
      init: `transform: translate${axis}(${dist}); opacity: 0;`,
      final: `transform: translate${axis}(0); opacity: 1; transition: all ${duration} ease ${delay};`,
      timeout: (parseFloat(duration) + parseFloat(delay)) * 1000
    };
  };

  const animate = el => {
    const css = getCSS(el);
    if (!el.hasAttribute('data-sr-init')) {
      el.style.cssText = css.init;
      el.setAttribute('data-sr-init', 'true');
    }

    if (!el.hasAttribute('data-sr-complete') && isInViewport(el)) {
      el.style.cssText = css.final;
      setTimeout(() => {
        el.removeAttribute('style');
        el.setAttribute('data-sr-complete', 'true');
      }, css.timeout);
    }
  };

  const init = () => {
    const elements = Array.from(document.querySelectorAll('[data-scrollreveal]'));
    const onScroll = () => elements.forEach(animate);

    window.addEventListener('scroll', onScroll);
    window.addEventListener('resize', onScroll);
    onScroll();
  };

  return { init };
})();

// ====================================
// MODULE: AjaxPosts
// ====================================
const AjaxPosts = (() => {
  let currentPage = 1;
  let wrapper, container;

  const getPerPage = () => {
    const w = window.innerWidth;
    return w <= 450 ? 4 : w <= 768 ? 6 : w <= 1024 ? 8 : 10;
  };

  const scrollTo = el => {
    if (!el) return;
    window.scrollTo({ top: el.offsetTop, behavior: 'smooth' });
  };

  const updatePagination = () => {
    document.querySelector('.pagination')?.remove();

    const pag = document.createElement('div');
    pag.className = 'pagination fade-slide';

    const prevBtn = document.createElement('button');
    prevBtn.className = 'prev';
    prevBtn.disabled = currentPage === 1;
    prevBtn.innerHTML = '<i class="ri-arrow-left-line"></i>';
    prevBtn.addEventListener('click', () => {
      if (currentPage > 1) loadPosts(--currentPage);
    });

    const nextBtn = document.createElement('button');
    nextBtn.className = 'next';
    nextBtn.innerHTML = '<i class="ri-arrow-right-line"></i>';
    nextBtn.addEventListener('click', () => loadPosts(++currentPage));

    pag.append(prevBtn, nextBtn);
    document.querySelector('.category-posts')?.appendChild(pag);
  };

  const injectHTMLSafely = (htmlString, target) => {
    const temp = document.createElement('div');
    temp.innerHTML = htmlString;

    const allowedTags = ['DIV', 'P', 'SPAN', 'A', 'IMG'];
    for (let el of temp.querySelectorAll('*')) {
      if (!allowedTags.includes(el.tagName)) el.remove();
    }

    target.innerHTML = ''; // Clear existing content
    while (temp.firstChild) {
      target.appendChild(temp.firstChild);
    }
  };

  const loadPosts = async (page = 1) => {
    try {
      const data = new FormData();
      data.append('action', 'load_tags_ajax');
      data.append('security', tags_ajax_obj?.nonce ?? '');
      data.append('page', page);
      data.append('posts_per_page', getPerPage());
      data.append('category', tags_ajax_obj?.category ?? '');

      const response = await fetch(tags_ajax_obj.ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data,
      });
      if (!response.ok) throw new Error('Network error loading posts');

      const html = await response.text();
      if (!html.trim()) throw new Error('Empty response');

      injectHTMLSafely(html, container);
      if (container === wrapper) updatePagination();
      scrollTo(container);
    } catch (err) {
      console.error('[AjaxPosts] Failed to load posts:', err.message);
      container.innerHTML = '<p class="error-msg">Failed to load posts. Please try again.</p>';
    }
  };

  const handleResize = () => {
    currentPage = 1;
    loadPosts(currentPage);
  };

  const init = () => {
    wrapper = document.querySelector('.category-wrapper');
    container = document.querySelector('#tag-posts-container') || wrapper;
    if (!container) return;

    window.addEventListener('resize', debounce(handleResize, 300));
    loadPosts(currentPage);
  };

  // Utility: simple debounce
  const debounce = (fn, delay) => {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  };

  return { init };
})();


// ====================================
// MODULE: Global
// ====================================
const Global = (() => {
  const resizeFaqImg = () => {
    const img = document.getElementById('faq-img');
    if (!img?.style) return;

    const w = window.innerWidth;
    img.style.width = w <= 450 ? '95%' : w <= 768 ? '75%' : '60%';
  };

  const handleScrollToTop = () => {
    const btn = document.getElementById('scrollToTopBtn');
    if (!btn) return;

    const showBtn =
      document.body.scrollTop > window.innerHeight * 0.2 ||
      document.documentElement.scrollTop > window.innerHeight * 0.5;

    btn.style.display = showBtn ? 'block' : 'none';
  };

  const setupScrollToTop = () => {
    const btn = document.getElementById('scrollToTopBtn');
    if (!btn) return;

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', handleScrollToTop);
  };

  const init = () => {
    resizeFaqImg();
    setupScrollToTop();
    window.addEventListener('resize', resizeFaqImg);
  };

  return { init };
})();

// ====================================
// MODULE: Preloader
// ====================================
function setupPreloader() {
  window.addEventListener('load', () => {
    setTimeout(() => {
      const preloader = document.getElementById('site-preloader');
      if (preloader) preloader.classList.add('hidden');
    }, 800);
  });
}

// ====================================
// MODULE: New Acquisition Toggle
// ====================================
const NewAcquisitionToggle = (() => {
  const toggles = () => document.querySelectorAll('.na-toggle');

  const collapseContent = content => {
    content.style.maxHeight = content.scrollHeight + 'px';
    void content.offsetHeight; // Force reflow
    content.classList.add('collapsing');
    content.style.maxHeight = '0';
    content.style.opacity = '0';

    content.addEventListener(
      'transitionend',
      e => {
        if (e.propertyName === 'max-height') {
          content.classList.remove('collapsing', 'na-visible');
          content.style.visibility = 'hidden';
          content.style.maxHeight = '';
        }
      },
      { once: true }
    );
  };

  const expandContent = content => {
    content.style.visibility = 'visible';
    content.classList.add('na-visible', 'expanding');
    content.style.opacity = '1';
    content.style.maxHeight = content.scrollHeight + 'px';

    content.addEventListener(
      'transitionend',
      e => {
        if (e.propertyName === 'max-height') {
          content.classList.remove('expanding');
          content.style.maxHeight = 'none';
        }
      },
      { once: true }
    );
  };

  const onClick = e => {
    const toggle = e.currentTarget;
    const content = toggle.nextElementSibling;
    if (!content) return;

    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isExpanded));

    content.classList.remove('expanding', 'collapsing');

    if (isExpanded) {
      collapseContent(content);
    } else {
      expandContent(content);
    }
  };

  const init = () => {
    toggles().forEach(toggle => toggle.addEventListener('click', onClick));
  };

  return { init };
})();
