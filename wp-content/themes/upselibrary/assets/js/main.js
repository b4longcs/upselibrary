//* Header Sticky *//
document.addEventListener("DOMContentLoaded", function () {
  const header = document.getElementById("main-header");
  

  window.addEventListener("scroll", function () {
      if (window.scrollY > 150) {
          header.classList.add("sticky");
          
      } else {
          header.classList.remove("sticky");
      }
  });
});

//* End of Header Sticky *//


//* Expanded Sub-Menu *//
(() => {
    document.addEventListener("DOMContentLoaded", () => {
      if (window.innerWidth > 1024) return;
  
      const labels = document.querySelectorAll(".menu-label");
  
      labels.forEach(label => {
        label.addEventListener("click", () => {
          const menuKey = label.dataset.menu;
          const subMenu = document.querySelector(`.sub-menu[data-submenu="${menuKey}"]`);
          const isOpen = subMenu?.classList.contains("open");
  
          document.querySelectorAll(".sub-menu").forEach(menu => {
            menu.classList.remove("open");
            menu.style.maxHeight = "";
          });
  
          document.querySelectorAll(".menu-label").forEach(l => {
            l.setAttribute("aria-expanded", "false");
          });

          if (subMenu && !isOpen) {
            subMenu.classList.add("open");
            subMenu.style.maxHeight = `${subMenu.scrollHeight}px`;
            label.setAttribute("aria-expanded", "true");
          }
        });
      });
    });
})();

//* End of Sub-Menu *//
  
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


//* TEST *//
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
