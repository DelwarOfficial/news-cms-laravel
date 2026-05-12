function articleUrl(baseUrl, slug) {
  if (!slug || slug === '#') {
    return '#';
  }

  return baseUrl.replace(/\/$/, '') + '/' + slug;
}

function initPhotoNewsBlock(block) {
  const dataEl = document.getElementById(block.dataset.photoNewsData || '');
  const slides = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

  if (!slides.length) {
    return;
  }

  const baseUrl = block.dataset.articleBase || '/article';
  const mainLink = block.querySelector('.photo-main-link');
  const mainImg = block.querySelector('.photo-main-img');
  const mainTitle = block.querySelector('.photo-main-title');
  const mainTime = block.querySelector('.photo-main-time span');
  const prevWrap = block.querySelector('.photo-prev');
  const nextWrap = block.querySelector('.photo-next');
  const prevButton = block.querySelector('.photo-btn-prev');
  const nextButton = block.querySelector('.photo-btn-next');
  const latestTab = block.querySelector('.photo-tab-latest');
  const popularTab = block.querySelector('.photo-tab-popular');
  const dots = Array.from(block.querySelectorAll('.photo-dots button'));
  const tabButtons = block.querySelectorAll('.photo-tab-btn');

  if (!mainLink || !mainImg || !mainTitle || !mainTime || !prevWrap || !nextWrap) {
    return;
  }

  let index = 0;

  function previewImage(slide, label) {
    return '<img src="' + slide.image_url + '" class="w-full h-full object-cover" alt="' + label + '">';
  }

  function render(nextIndex) {
    index = (nextIndex + slides.length) % slides.length;

    const current = slides[index];
    const previous = slides[(index - 1 + slides.length) % slides.length];
    const next = slides[(index + 1) % slides.length];

    mainLink.setAttribute('href', articleUrl(baseUrl, current.slug));
    mainImg.setAttribute('src', current.image_url);
    mainImg.setAttribute('alt', current.headline);
    mainTitle.textContent = current.headline;
    mainTime.textContent = current.timestamp;

    prevWrap.innerHTML = previewImage(previous, 'Previous');
    nextWrap.innerHTML = previewImage(next, 'Next');

    dots.forEach(function (dot, dotIndex) {
      dot.classList.toggle('bg-black', dotIndex === index);
      dot.classList.toggle('bg-gray-300', dotIndex !== index);
    });
  }

  if (prevButton) {
    prevButton.addEventListener('click', function () {
      render(index - 1);
    });
  }

  if (nextButton) {
    nextButton.addEventListener('click', function () {
      render(index + 1);
    });
  }

  prevWrap.addEventListener('click', function () {
    render(index - 1);
  });

  nextWrap.addEventListener('click', function () {
    render(index + 1);
  });

  dots.forEach(function (dot) {
    dot.addEventListener('click', function () {
      render(parseInt(dot.dataset.index, 10) || 0);
    });
  });

  tabButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      const isPopular = button.dataset.tab === 'popular';

      if (latestTab) {
        latestTab.classList.toggle('hidden', isPopular);
      }

      if (popularTab) {
        popularTab.classList.toggle('hidden', !isPopular);
      }

      tabButtons.forEach(function (tabButton) {
        const active = tabButton === button;

        tabButton.classList.toggle('border-[#e2231a]', active);
        tabButton.classList.toggle('text-fg', active);
        tabButton.classList.toggle('border-transparent', !active);
        tabButton.classList.toggle('text-fg-muted', !active);
      });
    });
  });

  render(0);
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-photo-news]').forEach(initPhotoNewsBlock);
});
