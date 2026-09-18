document.addEventListener('DOMContentLoaded', () => {

  // Remove nested transition attributes
  document.querySelectorAll('[data-on-scroll-transition]').forEach(parent => {
    parent.querySelectorAll('[data-on-scroll-transition]').forEach(child => {
      if (child !== parent) {
        child.removeAttribute('data-on-scroll-transition');
        child.removeAttribute('data-on-scroll-transition-order');
      }
    });
  });

  const defaultOptions = {
    selector: '[data-on-scroll-transition]:not(.visible)',
    visibleClass: 'visible',
    root: null,
    rootMargin: '0px',
    threshold: 0.1,
    delayIncrement: 100,
    maxDelay: 500,
    batchSizeThreshold: 100
  };

  // Make defaults reusable by filter script
  window.onScrollTransitionDefaults = { ...defaultOptions };

  function sortElements(elements) {
    return elements.sort((a, b) => {
      const orderA = parseInt(
        a.getAttribute('data-on-scroll-transition-order')
      );

      const orderB = parseInt(
        b.getAttribute('data-on-scroll-transition-order')
      );

      const hasOrderA = !isNaN(orderA);
      const hasOrderB = !isNaN(orderB);

      if (hasOrderA && hasOrderB && orderA !== orderB) {
        return orderA - orderB;
      }

      if (hasOrderA && !hasOrderB) return -1;
      if (!hasOrderA && hasOrderB) return 1;

      const rectA = a.getBoundingClientRect();
      const rectB = b.getBoundingClientRect();

      if (rectA.top !== rectB.top) {
        return rectA.top - rectB.top;
      }

      const position = a.compareDocumentPosition(b);

      if (position & Node.DOCUMENT_POSITION_FOLLOWING) return -1;
      if (position & Node.DOCUMENT_POSITION_PRECEDING) return 1;

      return 0;
    });
  }

  function getDelay(element, index, batchLength, options) {
    const orderAttr = parseInt(
      element.getAttribute('data-on-scroll-transition-order')
    );

    const isStagger =
      element.getAttribute('data-on-scroll-transition') === 'stagger';

    /*
     * Stagger starts one step after the intersection trigger so the first
     * element also has a deliberate pause before appearing. Fade effects
     * keep their existing timing and therefore still start at 0ms.
     */
    const initialStep = isStagger ? 1 : 0;

    if (!isNaN(orderAttr)) {
      return Math.min(
        (orderAttr + initialStep) * options.delayIncrement,
        options.maxDelay
      );
    }

    if (batchLength > options.batchSizeThreshold) {
      return isStagger ? options.delayIncrement : 0;
    }

    return Math.min(
      (index + initialStep) * options.delayIncrement,
      options.maxDelay
    );
  }

  function showBatch(elements, options) {
    const sortedElements = sortElements([...elements]);

    sortedElements.forEach((element, index) => {
      const delay = getDelay(
        element,
        index,
        sortedElements.length,
        options
      );

      element.style.transitionDelay = `${delay}ms`;
      element.classList.add(options.visibleClass);
    });
  }

  function observeBlocksChildren(options = {}) {
    const mergedOptions = { ...defaultOptions, ...options };

    const observerOptions = {
      root: mergedOptions.root,
      rootMargin: mergedOptions.rootMargin,
      threshold: mergedOptions.threshold
    };

    const observer = new IntersectionObserver((entries, observer) => {

      const intersectingElements = entries
        .filter(entry => entry.isIntersecting)
        .map(entry => entry.target);

      if (!intersectingElements.length) {
        return;
      }

      /*
       * Sort the complete observer batch before applying delays.
       * This keeps the visual order stable even when the browser
       * returns IntersectionObserver entries in a different order.
       */
      showBatch(intersectingElements, mergedOptions);

      intersectingElements.forEach(element => {
        observer.unobserve(element);
      });

    }, observerOptions);

    const elements = Array.from(
      document.querySelectorAll(mergedOptions.selector)
    );

    const initiallyVisible = [];
    const remaining = [];

    elements.forEach(element => {
      const rect = element.getBoundingClientRect();

      if (
        rect.top < window.innerHeight &&
        rect.bottom > 0
      ) {
        initiallyVisible.push(element);
      } else {
        remaining.push(element);
      }
    });

    /*
     * Handle all transition elements already intersecting the viewport
     * as one ordered initial batch.
     *
     * The two animation frames ensure the initial hidden state is painted
     * before the visible class is added.
     */
    if (initiallyVisible.length) {
      const sortedInitialElements = sortElements([
        ...initiallyVisible
      ]);

      sortedInitialElements.forEach((element, index) => {
        const delay = getDelay(
          element,
          index,
          sortedInitialElements.length,
          mergedOptions
        );

        element.style.transitionDelay = `${delay}ms`;
      });

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          sortedInitialElements.forEach(element => {
            element.classList.add(
              mergedOptions.visibleClass
            );
          });
        });
      });
    }

    // Observe all remaining transition elements normally
    remaining.forEach(element => {
      observer.observe(element);
    });
  }

  /*
   * Wait two frames before initialization so browsers such as Safari
   * have time to restore the previous scroll position after reload.
   */
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      /*
       * Organizers can still be settling their initial geometry while
       * self-hosted videos receive their metadata. If an organizer exposes
       * an initialReady promise, wait for that final geometry before taking
       * the viewport snapshot used for the first fade-in batch.
       *
       * Organizers without pending video geometry resolve immediately, so
       * ordinary pages keep the existing two-frame startup behavior.
       */
      const organizerReady = window.PureOrganizer?.initialReady;

      if (organizerReady && typeof organizerReady.then === 'function') {
        organizerReady
          .catch(() => undefined)
          .then(() => observeBlocksChildren());
      } else {
        observeBlocksChildren();
      }
    });
  });

  window.observeBlocksChildren = observeBlocksChildren;

});