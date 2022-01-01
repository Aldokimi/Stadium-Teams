const home = document.querySelector('.home');
const home_cata = document.querySelector('.home-cata');

const away = document.querySelector('.away');
const away_cata = document.querySelector('.away-cata');


home.addEventListener('click', ()=>{
    home_cata.style.visibility='visible';
});

away.addEventListener('click', ()=>{
    away_cata.style.visibility='visible';
});