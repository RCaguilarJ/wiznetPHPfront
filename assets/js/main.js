document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const expanded = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', String(!expanded));
      nav.classList.toggle('is-open');
    });
  }

  const fileInputs = document.querySelectorAll('.upload-field input[type="file"]');
  fileInputs.forEach((input) => {
    input.addEventListener('change', () => {
      const dropzone = input.parentElement?.querySelector('.upload-dropzone strong');
      if (!dropzone) {
        return;
      }

      dropzone.textContent = input.files && input.files.length > 0
        ? input.files[0].name
        : 'Suelta un archivo aqui o haz clic para subir';
    });
  });

  const accordionItems = document.querySelectorAll('.accordion details');

  const finishAccordionAnimation = (item, open) => {
    item.open = open;
    item.style.height = '';
    item.style.overflow = '';
    item.dataset.animating = 'false';
    item._accordionAnimation = null;
  };

  const collapseAccordionItem = (item, summary) => {
    if (item._accordionAnimation) {
      item._accordionAnimation.cancel();
    }

    item.dataset.animating = 'true';
    item.style.overflow = 'hidden';

    const startHeight = `${item.offsetHeight}px`;
    const endHeight = `${summary.offsetHeight}px`;

    item._accordionAnimation = item.animate(
      { height: [startHeight, endHeight] },
      { duration: 240, easing: 'ease-out' }
    );

    item._accordionAnimation.onfinish = () => finishAccordionAnimation(item, false);
    item._accordionAnimation.oncancel = () => {
      item.dataset.animating = 'false';
      item._accordionAnimation = null;
    };
  };

  const expandAccordionItem = (item) => {
    if (item._accordionAnimation) {
      item._accordionAnimation.cancel();
    }

    item.dataset.animating = 'true';
    item.style.overflow = 'hidden';
    item.style.height = `${item.offsetHeight}px`;
    item.open = true;

    requestAnimationFrame(() => {
      const startHeight = item.style.height;
      const endHeight = `${item.scrollHeight}px`;

      item._accordionAnimation = item.animate(
        { height: [startHeight, endHeight] },
        { duration: 260, easing: 'ease-out' }
      );

      item._accordionAnimation.onfinish = () => finishAccordionAnimation(item, true);
      item._accordionAnimation.oncancel = () => {
        item.dataset.animating = 'false';
        item._accordionAnimation = null;
      };
    });
  };

  const closeOtherAccordionItems = (currentItem) => {
    accordionItems.forEach((otherItem) => {
      if (otherItem === currentItem || !otherItem.open) {
        return;
      }

      const otherSummary = otherItem.querySelector('summary');
      if (!otherSummary) {
        return;
      }

      if (prefersReducedMotion) {
        otherItem.open = false;
        return;
      }

      if (otherItem.dataset.animating === 'true') {
        return;
      }

      collapseAccordionItem(otherItem, otherSummary);
    });
  };

  accordionItems.forEach((item) => {
    const summary = item.querySelector('summary');
    if (!summary) {
      return;
    }

    item.dataset.animating = 'false';

    summary.addEventListener('click', (event) => {
      if (prefersReducedMotion) {
        return;
      }

      event.preventDefault();

      if (item.dataset.animating === 'true') {
        return;
      }

      if (item.open) {
        collapseAccordionItem(item, summary);
      } else {
        closeOtherAccordionItems(item);
        expandAccordionItem(item);
      }
    });
  });

  const contractModalTriggers = document.querySelectorAll('[data-contract-modal-open]');

  if (contractModalTriggers.length > 0) {
    const openContractModal = (modal) => {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closeContractModal = (modal) => {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    contractModalTriggers.forEach((trigger) => {
      trigger.addEventListener('click', () => {
        const modalName = trigger.getAttribute('data-contract-modal-open');
        const modal = document.querySelector(`[data-contract-modal="${modalName}"]`);

        if (!modal) {
          return;
        }

        openContractModal(modal);
      });
    });

    const contractModals = document.querySelectorAll('[data-contract-modal]');

    contractModals.forEach((modal) => {
      modal.querySelectorAll('[data-contract-modal-close]').forEach((closer) => {
        closer.addEventListener('click', () => {
          closeContractModal(modal);
        });
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') {
        return;
      }

      document.querySelectorAll('[data-contract-modal].is-open').forEach((modal) => {
        closeContractModal(modal);
      });
    });
  }
});
