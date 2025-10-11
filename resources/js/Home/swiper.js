
  const swiper = new Swiper(".swiper", {
    loop: true,
    pagination: { el: ".swiper-pagination", clickable: true },
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    slidesPerView: 1,
    spaceBetween: 20,
    autoplay: { delay: 3000, disableOnInteraction: false },
  });
