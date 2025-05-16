
document.addEventListener('DOMContentLoaded', () => {
  Header.init();
  Tabs.init();
  ScrollReveal.init();
  AjaxPosts.init();
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

    navMenus.forEach(item => {
      const subMenu = item.querySelector('.sub-menu');

      item.addEventListener('click', e => {
        if (e.target.tagName === 'A') return;

        const isOpen = item.classList.contains('open');
        if (subMenu) {
          e.preventDefault();
          e.stopPropagation();

          navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
          if (!isOpen) item.classList.add('open', 'active-indicator');
        } else {
          navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
        }
      });

      item.addEventListener('dblclick', () => {
        item.classList.remove('open', 'active-indicator');
      });

      item.addEventListener('mouseenter', () => {
        if (window.innerWidth > 1024 && !item.classList.contains('open')) {
          item.classList.add('active-indicator');
        }
      });

      item.addEventListener('mouseleave', () => {
        if (window.innerWidth > 1024 && !item.classList.contains('open')) {
          item.classList.remove('active-indicator');
        }
      });
    });
  };

  const handleScroll = () => {
    const currentScroll = window.scrollY;
    if (currentScroll < window.innerWidth * 0.5) {
      headerSticky.classList.remove('header-hidden');
    } else {
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

    const tabs = tabList.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');
    const indicator = document.querySelector('.tabs-indicator');

    const setIndicatorPosition = tab => {
      indicator.style.transform = `translateX(${tab.offsetLeft}px)`;
      indicator.style.width = `${tab.offsetWidth}px`;
    };

    const switchTab = tab => {
      tabs.forEach(t => {
        t.classList.remove('active');
        t.setAttribute('aria-selected', false);
      });

      panels.forEach(p => p.setAttribute('aria-hidden', true));

      tab.classList.add('active');
      tab.setAttribute('aria-selected', true);

      const targetPanel = document.getElementById(tab.getAttribute('aria-controls'));
      if (targetPanel) {
        targetPanel.setAttribute('aria-hidden', false);
      }

      setIndicatorPosition(tab);
    };

    if (tabs.length) {
      const firstTab = tabs[0];
      const firstPanel = document.getElementById(firstTab.getAttribute('aria-controls'));

      setIndicatorPosition(firstTab);
      firstTab.classList.add('active');
      firstTab.setAttribute('aria-selected', true);
      if (firstPanel) firstPanel.setAttribute('aria-hidden', false);
    }

    tabs.forEach(tab => tab.addEventListener('click', e => switchTab(e.currentTarget)));

    tabList.addEventListener('keydown', e => {
      const currentTab = document.activeElement;
      let newTab = null;

      if (e.key === 'ArrowLeft') {
        newTab = currentTab.previousElementSibling;
      } else if (e.key === 'ArrowRight') {
        newTab = currentTab.nextElementSibling;
      }

      if (newTab && newTab.classList.contains('tab-button')) {
        newTab.click();
        newTab.focus();
      }
    });
  };

  return { init };
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
    const tokens = el.getAttribute('data-scrollreveal').split(/[, ]+/).filter(w => !['from', 'the', 'and', 'then', 'but'].includes(w));
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
      el.setAttribute('style', css.init);
      el.setAttribute('data-sr-init', 'true');
    }

    if (!el.hasAttribute('data-sr-complete') && isInViewport(el)) {
      el.setAttribute('style', css.final);
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
    window.scrollTo({ top: el.offsetTop, behavior: 'smooth' });
  };

  const updatePagination = () => {
    document.querySelector('.pagination')?.remove();

    const pag = document.createElement('div');
    pag.className = 'pagination fade-slide';
    pag.innerHTML = `
      <button class="prev" ${currentPage === 1 ? 'disabled' : ''}><i class="ri-arrow-left-line"></i></button>
      <button class="next"><i class="ri-arrow-right-line"></i></button>
    `;
    document.querySelector('.category-posts')?.appendChild(pag);

    pag.querySelector('.prev')?.addEventListener('click', () => {
      if (currentPage > 1) loadPosts(--currentPage);
    });
    pag.querySelector('.next')?.addEventListener('click', () => {
      loadPosts(++currentPage);
    });
  };

  const loadPosts = (page = 1) => {
    const data = new FormData();
    data.append('action', 'load_tags_ajax');
    data.append('security', tags_ajax_obj.nonce);
    data.append('page', page);
    data.append('posts_per_page', getPerPage());
    data.append('category', tags_ajax_obj.category);

    fetch(tags_ajax_obj.ajaxurl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data
    })
    .then(res => res.text())
    .then(html => {
      container.innerHTML = html;
      if (container === wrapper) updatePagination();
      scrollTo(container);
    });
  };

  const handleResize = () => {
    currentPage = 1;
    loadPosts(currentPage);
  };

  const init = () => {
    wrapper = document.querySelector('.category-wrapper');
    container = document.querySelector('#tag-posts-container') || wrapper;
    if (!container) return;

    window.addEventListener('resize', handleResize);
    loadPosts(currentPage);
  };

  return { init };
})();
