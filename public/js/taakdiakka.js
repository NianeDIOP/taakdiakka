/* avatars monogramme — neutres */
document.querySelectorAll('.av[data-av]').forEach(el=>el.textContent=el.dataset.av.trim());

/* progression */
const progress=document.getElementById('progress'),nav=document.getElementById('nav');
addEventListener('scroll',()=>{
  nav.classList.toggle('scrolled',scrollY>40);
  const h=document.documentElement.scrollHeight-innerHeight;
  progress.style.width=(scrollY/h*100)+'%';
});

/* burger */
const burger=document.getElementById('burger'),navlinks=document.getElementById('navlinks');
burger.addEventListener('click',()=>{navlinks.classList.toggle('open');document.body.classList.toggle('no-scroll');});
navlinks.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{navlinks.classList.remove('open');document.body.classList.remove('no-scroll');}));

/* révélations */
const io=new IntersectionObserver(es=>es.forEach(en=>{if(en.isIntersecting){en.target.classList.add('in');io.unobserve(en.target);}}),{threshold:.12});
document.querySelectorAll('.reveal:not(.in)').forEach(el=>io.observe(el));

/* donut */
const donut=document.getElementById('donut');
if(donut){new IntersectionObserver((e,o)=>e.forEach(en=>{if(en.isIntersecting){donut.classList.add('in');o.disconnect();}}),{threshold:.4}).observe(donut);}

/* compteurs */
function count(el){const target=+el.dataset.count,sfx=el.dataset.suffix||'',dur=2000,t0=performance.now();
  (function tick(now){const p=Math.min((now-t0)/dur,1),e=1-Math.pow(1-p,3);
    el.textContent=Math.floor(e*target).toLocaleString('fr-FR')+sfx;
    if(p<1)requestAnimationFrame(tick);else el.textContent=target.toLocaleString('fr-FR')+sfx;})(t0);}
const co=new IntersectionObserver(es=>es.forEach(en=>{if(en.isIntersecting){count(en.target);co.unobserve(en.target);}}),{threshold:.5});
document.querySelectorAll('[data-count]').forEach(c=>co.observe(c));

/* toggle anonymat */
const anonRow=document.getElementById('anonRow');
if(anonRow){anonRow.addEventListener('click',()=>anonRow.querySelector('.toggle').classList.toggle('off'));}

/* like */
document.querySelectorAll('.p-btn.like').forEach(btn=>btn.addEventListener('click',()=>btn.classList.toggle('liked')));

/* onglets */
document.querySelectorAll('.feed-tabs .tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('.feed-tabs .tab').forEach(x=>x.classList.remove('active'));t.classList.add('active');}));
