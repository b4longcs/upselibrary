//* Header *//
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');
const closeBtn = document.getElementById('close-btn');
const overlay = document.getElementById('overlay');
const navMenus = document.querySelectorAll('.nav-links > li');

// Open sidebar
hamburger.addEventListener('click', () => {
  navLinks.classList.add('active');
  overlay.classList.add('active');
});

// Close sidebar
function closeSidebar() {
  navLinks.classList.remove('active');
  overlay.classList.remove('active');
  navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
}

closeBtn.addEventListener('click', closeSidebar);
overlay.addEventListener('click', closeSidebar);

// Handle each nav item
navMenus.forEach(item => {
  const hasSubMenu = item.querySelector('.sub-menu');

  item.addEventListener('click', (e) => {
    e.stopPropagation();

    const isOpen = item.classList.contains('open');

    // If no sub-menu, close all others
    if (!hasSubMenu) {
      navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
      return;
    }

    // If this one is not open, open it and close others
    if (!isOpen) {
      navMenus.forEach(li => li.classList.remove('open', 'active-indicator'));
      item.classList.add('open', 'active-indicator');
    }
  });

  // Double-click closes the current submenu if open
  if (hasSubMenu) {
    item.addEventListener('dblclick', (e) => {
      e.stopPropagation();
      item.classList.remove('open', 'active-indicator');
    });
  }

  // Hover for desktop
  item.addEventListener('mouseenter', () => {
    if (window.innerWidth > 1024) {
      navMenus.forEach(li => {
        if (!li.classList.contains('open')) {
          li.classList.remove('active-indicator');
        }
      });
      if (!item.classList.contains('open')) {
        item.classList.add('active-indicator');
      }
    }
  });

  item.addEventListener('mouseleave', () => {
    if (window.innerWidth > 1024 && !item.classList.contains('open')) {
      item.classList.remove('active-indicator');
    }
  });
});


// Scroll up to reveal, scroll down to hide //
let lastScrollTop = 0;
const header = document.querySelector('.header-sticky');

window.addEventListener('scroll', function () {
  const scrollThreshold = window.innerWidth * 0.3;
  const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

  if (currentScroll < scrollThreshold) {
    header.classList.remove('header-hidden');
    return;
  }

  if (currentScroll > lastScrollTop) {
    header.classList.add('header-hidden');
  } else {
    header.classList.remove('header-hidden');
  }

  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});



// ProgressBar //
let body = document.querySelector('body');
let barWidthProp = '--progress-bar-width';
let totalHeight = body.clientHeight;

let setProgressBar = () => {
	let scrollRatio = (window.innerHeight + window.scrollY) * 100  / totalHeight;
	
	body.style.setProperty(barWidthProp, scrollRatio + '%');	
};

setProgressBar();

window.addEventListener('scroll', e => {
	setProgressBar();
})

window.addEventListener('resize', e => {
	totalHeight = body.clientHeight;
	setProgressBar();
})



//* End of Header *//
  
//* Animated Text *//
const words = ["Learn", "Explore", "Discover"];
const text = document.querySelector(".text-animated");

const gen = (function* () {
  let index = 0;
  while (true) {
    yield index;
    index = (index + 1) % words.length;
  }
})();

const printChar = (word) => {
  let i = 0;
  text.textContent = "";
  const interval = setInterval(() => {
    if (i >= word.length) {
      clearInterval(interval);
      deleteChar();
    } else {
      text.textContent += word[i++];
    }
  }, 200);
};

const deleteChar = () => {
  let i = text.textContent.length;
  const interval = setInterval(() => {
    if (i > 0) {
      text.textContent = text.textContent.slice(0, --i);
    } else {
      clearInterval(interval);
      printChar(words[gen.next().value]);
    }
  }, 100);
};

printChar(words[gen.next().value]);

//* End of Animated Text *//

//* Tabs --code pen*//
document.addEventListener('DOMContentLoaded', () => {
    const tabList = document.querySelector('.tabs-nav');
    const tabs = tabList.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');
    const indicator = document.querySelector('.tabs-indicator');

    const setIndicatorPosition = (tab) => {
        indicator.style.transform = `translateX(${tab.offsetLeft}px)`;
        indicator.style.width = `${tab.offsetWidth}px`;
    };

    setIndicatorPosition(tabs[0]);

    tabs.forEach((tab) => {
        tab.addEventListener('click', (e) => {
            const targetTab = e.target;
            const targetPanel = document.querySelector(
                `#${targetTab.getAttribute('aria-controls')}`
            );

            tabs.forEach((tab) => {
                tab.setAttribute('aria-selected', false);
                tab.classList.remove('active');
            });
            targetTab.setAttribute('aria-selected', true);
            targetTab.classList.add('active');

            panels.forEach((panel) => {
                panel.setAttribute('aria-hidden', true);
            });
            targetPanel.setAttribute('aria-hidden', false);

            setIndicatorPosition(targetTab);
        });
    });

    tabList.addEventListener('keydown', (e) => {
        const targetTab = e.target;
        const previousTab = targetTab.previousElementSibling;
        const nextTab = targetTab.nextElementSibling;

        if (e.key === 'ArrowLeft' && previousTab) {
            previousTab.click();
            previousTab.focus();
        }
        if (e.key === 'ArrowRight' && nextTab) {
            nextTab.click();
            nextTab.focus();
        }
    });
});
//*END of tabs*//


((window) => {
  'use strict';

  const docElem = window.document.documentElement;

  const getViewportH = () => {
    const client = docElem.clientHeight;
    const inner = window.innerHeight;
    return client < inner ? inner : client;
  };

  const getOffset = (el) => {
    let offsetTop = 0, offsetLeft = 0;
    while (el) {
      if (!isNaN(el.offsetTop)) offsetTop += el.offsetTop;
      if (!isNaN(el.offsetLeft)) offsetLeft += el.offsetLeft;
      el = el.offsetParent;
    }
    return { top: offsetTop, left: offsetLeft };
  };

  const isElementInViewport = (el, h = 0) => {
    const scrolled = window.pageYOffset;
    const viewed = scrolled + getViewportH();
    const elTop = getOffset(el).top;
    const elBottom = elTop + el.offsetHeight;
    return (elTop + el.offsetHeight * h) <= viewed && elBottom >= scrolled;
  };

  const parseLanguage = (el) => {
    const words = el.getAttribute('data-scrollreveal').split(/[, ]+/);
    const parsed = {};
    let enterFrom;

    const filter = (words) => {
      const blacklist = ["from", "the", "and", "then", "but"];
      return words.filter(word => !blacklist.includes(word));
    };

    const filteredWords = filter(words);

    filteredWords.forEach((word, i) => {
      switch (word) {
        case "enter":
          enterFrom = filteredWords[i + 1];
          parsed.axis = (enterFrom === "top" || enterFrom === "bottom") ? "y" : "x";
          parsed.from = enterFrom;
          break;
        case "after":
        case "wait":
          parsed.delay = filteredWords[i + 1];
          break;
        case "move":
          parsed.distance = filteredWords[i + 1];
          break;
        case "over":
          parsed.duration = filteredWords[i + 1];
          break;
        case "trigger":
          parsed.eventName = filteredWords[i + 1];
          break;
      }
    });

    if (enterFrom === "top" || enterFrom === "left") {
      parsed.distance = `-${parsed.distance || "25px"}`;
    }

    return parsed;
  };

  const genCSS = (el) => {
    const parsed = parseLanguage(el);
    const dist = parsed.distance || "25px";
    const dur = parsed.duration || "0.66s";
    const delay = parsed.delay || "0s";
    const axis = parsed.axis || "y";

    return {
      initial: `
        transform: translate${axis}(${dist});
        opacity: 0;
      `,
      target: `
        transform: translate${axis}(0);
        opacity: 1;
      `,
      transition: `
        transition: all ${dur} ease ${delay};
      `,
      totalDuration: (parseFloat(dur) + parseFloat(delay)) * 1000
    };
  };

  const animate = (el) => {
    const css = genCSS(el);

    if (!el.hasAttribute('data-sr-init')) {
      el.setAttribute('style', css.initial);
      el.setAttribute('data-sr-init', 'true');
    }

    if (el.hasAttribute('data-sr-complete')) return;

    if (isElementInViewport(el, 0.33)) {
      el.setAttribute('style', css.target + css.transition);
      setTimeout(() => {
        el.removeAttribute('style');
        el.setAttribute('data-sr-complete', 'true');
      }, css.totalDuration);
    }
  };

  const scrollRevealInit = () => {
    const elems = [...document.querySelectorAll('[data-scrollreveal]')];
    
    const scrollHandler = () => {
      elems.forEach(el => animate(el));
    };

    window.addEventListener('scroll', scrollHandler);
    window.addEventListener('resize', scrollHandler);
    scrollHandler(); // fire on load
  };

  document.addEventListener('DOMContentLoaded', scrollRevealInit);

})(window);
