/* Shared, dependency-free interactions. No personal data is sent to a server. */
(()=>{
  'use strict';
  const {products,categories}=window.ShalangCatalog;
  const core=window.ShalangCore;
  const $=(s,p=document)=>p.querySelector(s), $$=(s,p=document)=>Array.from(p.querySelectorAll(s));
  const fa=n=>Number(n).toLocaleString('fa-IR');
  const escape=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const svg={arrow:'m14 7-5 5 5 5M9 12h12',cart:'M3 3h2l3 12h10l3-9H6M9 21h.01M18 21h.01',compare:'M5 4v16M19 4v16M2 8h6M16 16h6M12 4v16',plus:'M12 5v14M5 12h14',minus:'M5 12h14',trash:'M3 6h18M9 6V3h6v3M6 6l1 15h10l1-15M10 10v7M14 10v7',scan:'M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5M7 12h10M12 7v10'};
  const icon=name=>`<svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${svg[name]||svg.scan}"/></svg>`;
  const cartKey='shalangbin.cart.v2', compareKey='shalangbin.compare.v2';
  let storageWarning=false;
  function read(key,fallback){try{return JSON.parse(localStorage.getItem(key))??fallback;}catch{return fallback;}}
  function save(key,value){try{localStorage.setItem(key,JSON.stringify(value));return true;}catch{if(!storageWarning){storageWarning=true;toast('ذخیره در مرورگر در دسترس نیست؛ انتخاب‌ها فقط در همین صفحه باقی می‌مانند.');}return false;}}
  const cleanCompare=value=>Array.isArray(value)?[...new Set(value)].filter(id=>products.some(p=>p.id===id)).slice(0,3):[];
  let cart=core.cleanCart(read(cartKey,[]),products), comparison=cleanCompare(read(compareKey,[]));
  let toastTimer;
  function toast(message){const box=$('.toast');clearTimeout(toastTimer);box.textContent=message;box.hidden=false;toastTimer=setTimeout(()=>box.hidden=true,4000);}
  function sync(){
    $$('[data-cart-count]').forEach(el=>el.textContent=fa(cart.reduce((sum,x)=>sum+x.qty,0)));
    $$('[data-compare]').forEach(btn=>{const active=comparison.includes(btn.dataset.compare);btn.setAttribute('aria-pressed',String(active));const p=products.find(x=>x.id===btn.dataset.compare);btn.setAttribute('aria-label',`${active?'حذف':'افزودن'} ${p.name} ${active?'از':'به'} مقایسه`);});
    $('[data-compare-count]').textContent=fa(comparison.length);
    $('.compare-tray').hidden=!comparison.length||Boolean($('#comparison-content'));
    document.body.classList.toggle('has-compare',comparison.length>0&&!$('#comparison-content'));
  }
  const cartTotal=()=>cart.reduce((sum,row)=>sum+products.find(p=>p.id===row.id).price*row.qty,0);
  function summary(checkout=false){return `<div class="summary-card"><h2>خلاصه انتخاب‌ها</h2>${checkout?cart.map(row=>{const p=products.find(p=>p.id===row.id);return `<div class="summary-line"><span>${p.name}<br><small>${fa(row.qty)} عدد</small></span><b>${fa(p.price*row.qty)}</b></div>`;}).join(''):''}<div class="summary-line"><span>تعداد دستگاه</span><b>${fa(cart.reduce((s,x)=>s+x.qty,0))}</b></div><div class="summary-line"><span>هزینه ارسال</span><span>تعیین نشده</span></div><div class="summary-line summary-total"><span>جمع نمونه</span><span>${fa(cartTotal())} <small>تومان</small></span></div>${!checkout?'<a class="btn btn-primary full" href="checkout.html">مرور انتخاب‌ها '+icon('arrow')+'</a>':''}<p class="small-muted">قیمت و موجودی تأیید نشده؛ این سبد سفارش واقعی نیست.</p></div>`;}
  const empty=(title,description)=>`<div class="empty-state">${icon('cart')}<h2>${title}</h2><p>${description}</p><a class="btn btn-primary" href="shop.html">دیدن دستگاه‌ها ${icon('arrow')}</a></div>`;
  let cartUndo=null;
  function renderCart(){
    const target=$('#cart-content');
    if(target)target.innerHTML=cart.length?`<div class="cart-layout"><div><div class="cart-list">${cart.map(row=>{const p=products.find(p=>p.id===row.id);return `<article class="cart-item"><a href="${p.slug}"><img src="assets/images/${p.image}" alt="${p.name}" width="95" height="95"></a><div class="cart-item-info"><h2><a href="${p.slug}">${p.name}</a></h2><p>کابل ${fa(p.length)} متر • قیمت واحد ${fa(p.price)} تومان</p><strong>${fa(p.price*row.qty)} <small>تومان</small></strong></div><div class="cart-controls"><div class="quantity"><button data-qty="${p.id}" data-delta="1" aria-label="افزایش تعداد ${p.name}" ${row.qty>=99?'disabled':''}>${icon('plus')}</button><output aria-label="تعداد ${p.name}">${fa(row.qty)}</output><button data-qty="${p.id}" data-delta="-1" aria-label="کاهش تعداد ${p.name}" ${row.qty<=1?'disabled':''}>${icon('minus')}</button></div><button class="icon-button remove-item" data-remove="${p.id}" aria-label="حذف ${p.name}">${icon('trash')}</button></div></article>`;}).join('')}</div><div class="cart-bottom"><a class="text-link" href="shop.html">ادامه انتخاب ${icon('arrow')}</a><button class="link-button" data-empty-cart>خالی کردن سبد</button></div></div>${summary()}</div>`:empty('سبد شما هنوز خالی است','دستگاه‌ها را بررسی کنید و گزینه‌های مورد نظر را به سبد اضافه کنید.');
    if(target){
      const stage=document.createElement('div');stage.className='cart-stage';stage.innerHTML='<span class="current" aria-current="step"><b>۱</b> بررسی سبد</span><i aria-hidden="true"></i><span><b>۲</b> مرور انتخاب‌ها</span>';target.prepend(stage);
      if(cartUndo){const undo=document.createElement('div');undo.className='cart-undo';undo.innerHTML='<span role="status">انتخاب‌ها از سبد حذف شدند.</span><button data-undo-cart>بازگرداندن</button>';stage.after(undo);}
      if(cart.length){const title=document.createElement('div');title.className='cart-list-title';title.innerHTML=`<h2>محصولات سبد شما</h2><span>${fa(cart.length)} مدل · ${fa(cart.reduce((s,x)=>s+x.qty,0))} عدد</span>`;$('.cart-list',target).before(title);$$('.remove-item',target).forEach(button=>button.insertAdjacentHTML('beforeend','<span>حذف محصول</span>'));const guide=document.createElement('div');guide.className='cart-guidance';guide.innerHTML=icon('scan')+'<div><b>پیش از ادامه، مشخصات انتخابتان را بررسی کنید.</b><p>طول کابل، قطر هد و سازگاری با محیط کار را تأیید کنید. قیمت‌ها نمونه‌اند؛ پرداخت یا ثبت سفارش واقعی انجام نمی‌شود.</p></div>';$('.cart-bottom',target).after(guide);}
    }
    if($('#checkout-summary')){$('#checkout-summary').innerHTML=cart.length?summary(true):empty('انتخابی در سبد نیست','ابتدا یک دستگاه انتخاب کنید.');$('#download-order').disabled=!cart.length;}
  }
  let differencesOnly=false;
  function renderComparison(){
    const target=$('#comparison-content');if(!target)return;
    const selected=comparison.map(id=>products.find(p=>p.id===id));
    const rows=[['قیمت نمونه',p=>fa(p.price)+' تومان'],['طول کابل',p=>fa(p.length)+' متر'],['قطر هد',p=>fa(p.diameter)+' میلی‌متر'],['رزولوشن نمونه',p=>p.resolution],['کلاس درج‌شده هد',p=>p.waterproof],['ضبط تصویر',p=>p.recording?'در اطلاعات نمونه درج شده':'وابسته به دستگاه؛ تأیید نشده'],['کاربرد',p=>p.tag],['محدودیت',p=>p.limitation]];
    if(selected.length<2)differencesOnly=false;
    const diffCount=rows.filter(([,get])=>new Set(selected.map(get)).size>1).length;
    target.innerHTML=`<div class="compare-workspace"><div class="compare-toolbar"><div><span class="eyebrow">مقایسه بر اساس نیاز شما</span><h2>${fa(selected.length)} محصول انتخاب شده <small>از ۳ محصول</small></h2></div><label class="difference-switch"><input type="checkbox" data-differences ${differencesOnly?'checked':''} ${selected.length<2?'disabled':''}> فقط تفاوت‌ها</label></div><div class="compare-picker"><div><b>انتخاب دستگاه‌ها</b><p>تا سه گزینه را کنار هم قرار دهید.</p></div><div class="compare-picker-items">${products.map(p=>`<button data-compare="${p.id}" aria-pressed="${comparison.includes(p.id)}" ${selected.length===3&&!comparison.includes(p.id)?'disabled':''}><img src="assets/images/${p.image}" alt="" width="44" height="44"><span>${p.name}</span><b aria-hidden="true">${comparison.includes(p.id)?'✓':'+'}</b></button>`).join('')}</div></div><p class="compare-status" role="status">${selected.length<2?'برای دیدن تفاوت‌ها، دست‌کم دو محصول انتخاب کنید.':`${fa(diffCount)} ردیف متفاوت؛ خانه‌های رنگی تفاوت دستگاه‌ها را نشان می‌دهند.`}</p>${selected.some(p=>p.categories.includes('accessory'))?'<p class="compare-caution">هد دوربین یک قطعه جانبی است؛ قیمت و امکانات آن را معادل یک دستگاه کامل در نظر نگیرید.</p>':''}${selected.length?`<p class="compare-scroll-hint">${icon('compare')} جدول را افقی و عمودی پیمایش کنید؛ نام دستگاه‌ها و عنوان مشخصات در دسترس می‌مانند.</p><div class="comparison-scroll compare-matrix" role="region" aria-label="جدول مقایسه محصولات؛ قابل پیمایش افقی و عمودی" tabindex="0"><table class="comparison-table"><caption class="sr-only">مقایسه مشخصات نمونه محصولات انتخاب‌شده</caption><thead><tr><th scope="col"><span class="eyebrow">انتخاب دقیق‌تر</span>مشخصات دستگاه</th>${selected.map(p=>`<th scope="col" class="compare-product"><div class="compare-product-head"><img src="assets/images/${p.image}" alt="" width="64" height="64"><div><span>${p.tag}</span><h3><a href="${p.slug}">${p.name}</a></h3></div></div><strong>${fa(p.price)} <small>تومان</small></strong><a class="btn btn-outline" href="${p.slug}">بررسی و خرید ${icon('arrow')}</a><button class="link-button comparison-remove" data-compare="${p.id}">حذف از مقایسه</button></th>`).join('')}</tr></thead><tbody>${rows.map(([label,get])=>{const values=selected.map(get),different=new Set(values).size>1;return `<tr class="${different?'different':'same'}" ${differencesOnly&&!different?'hidden':''}><th scope="row">${label}${different?'<span class="difference-marker">متفاوت</span>':''}</th>${values.map(v=>`<td>${v}</td>`).join('')}</tr>`;}).join('')}</tbody></table></div><div class="cart-bottom"><p class="small-muted">قیمت‌ها و مشخصات نمونه‌اند؛ موجودی، ضمانت و تناسب با محیط باید تأیید شوند.</p><button class="link-button" data-clear-compare>پاک کردن مقایسه</button></div>`:`<div class="compare-empty">${icon('compare')}<h2>انتخاب را از همین‌جا شروع کنید.</h2><p>از فهرست بالا دو دستگاه انتخاب کنید تا تفاوت‌ها مشخص شوند.</p><a class="text-link" href="quiz.html">هنوز نمی‌دانم چه دستگاهی مناسب است ${icon('arrow')}</a></div>`}</div><div class="compare-advice"><div>${icon('scan')}<span><b>عدد بزرگ‌تر، همیشه انتخاب بهتر نیست.</b><p>طول کابل، قطر هد و امکانات را با مسیر واقعی کارتان تطبیق دهید.</p></span></div><a class="btn btn-outline" href="quiz.html">راهنمای انتخاب ${icon('arrow')}</a></div>`;
  }
  document.addEventListener('change',event=>{if(event.target.matches('[data-differences]')){differencesOnly=event.target.checked;$$('.compare-matrix tr.same').forEach(row=>row.hidden=differencesOnly);}});
  document.addEventListener('click',event=>{
    if(event.target.closest('[data-undo-cart]')&&cartUndo){cart=core.cleanCart(cartUndo,products);cartUndo=null;save(cartKey,cart);renderCart();sync();$('#cart-content .quantity button')?.focus({preventScroll:true});toast('انتخاب‌ها به سبد بازگردانده شدند.');return;}
    const add=event.target.closest('[data-add]');
    if(add){const id=add.dataset.add;if(!products.some(p=>p.id===id))return;const row=cart.find(x=>x.id===id);if(row&&row.qty>=99){toast('حداکثر تعداد هر دستگاه ۹۹ عدد است.');return;}if(row)row.qty++;else cart.push({id,qty:1});const stored=save(cartKey,cart);sync();renderCart();if(stored)toast('به سبد نمونه اضافه شد. سبد خرید از بالای صفحه در دسترس است.');}
    const cmp=event.target.closest('[data-compare]');
    if(cmp){const id=cmp.dataset.compare;if(!products.some(p=>p.id===id))return;if(comparison.includes(id))comparison=comparison.filter(x=>x!==id);else if(comparison.length<3)comparison.push(id);else{toast('تا سه دستگاه قابل مقایسه است. ابتدا یکی را حذف کنید.');return;}save(compareKey,comparison);renderComparison();sync();$(`.compare-picker [data-compare="${id}"]`)?.focus({preventScroll:true});}
    if(event.target.closest('[data-clear-compare]')){comparison=[];save(compareKey,comparison);renderComparison();sync();$('.compare-picker button')?.focus({preventScroll:true});}
    const qty=event.target.closest('[data-qty]'),remove=event.target.closest('[data-remove]'),clear=event.target.closest('[data-empty-cart]');
    if(qty||remove||clear){if(remove||clear)cartUndo=cart.map(row=>({...row}));else cartUndo=null;if(qty){const row=cart.find(x=>x.id===qty.dataset.qty);if(row)row.qty=Math.max(1,Math.min(99,row.qty+Number(qty.dataset.delta)));}if(remove)cart=cart.filter(x=>x.id!==remove.dataset.remove);if(clear)cart=[];save(cartKey,cart);renderCart();sync();if(qty){const next=$$('[data-qty]').find(x=>x.dataset.qty===qty.dataset.qty&&x.dataset.delta===qty.dataset.delta&&!x.disabled)||$$('[data-qty]').find(x=>x.dataset.qty===qty.dataset.qty&&!x.disabled);next?.focus({preventScroll:true});}else $('[data-undo-cart]')?.focus({preventScroll:true});toast(remove?'دستگاه از سبد حذف شد.':clear?'سبد خالی شد.':'تعداد و جمع قیمت به‌روز شد.');}
  });
  const menu=$('.menu-toggle'),nav=$('#main-nav');
  function closeMenu(returnFocus=false){nav.classList.remove('is-open');menu.setAttribute('aria-expanded','false');menu.setAttribute('aria-label','باز کردن منو');if(returnFocus)menu.focus();}
  menu.addEventListener('click',()=>{const open=!nav.classList.contains('is-open');nav.classList.toggle('is-open',open);menu.setAttribute('aria-expanded',String(open));menu.setAttribute('aria-label',open?'بستن منو':'باز کردن منو');});
  document.addEventListener('keydown',event=>{if(event.key==='Escape'&&nav.classList.contains('is-open'))closeMenu(true);});
  document.addEventListener('click',event=>{if(!event.target.closest('.site-header'))closeMenu();});
  nav.addEventListener('click',event=>{if(event.target.closest('a'))closeMenu();});
  matchMedia('(min-width:901px)').addEventListener('change',event=>{if(event.matches)closeMenu();});
  const params=new URLSearchParams(location.search);
  if($('#shop-products')){
    const queryInput=$('#header-q'),grid=$('#shop-products'),cards=new Map($$('[data-product-card]',grid).map(el=>[el.dataset.productCard,el]));
    queryInput.value=params.get('q')||'';
    const initialCategory=params.get('category');$$('input[name=category]').forEach(r=>r.checked=r.value===(categories.some(c=>c.id===initialCategory)||initialCategory==='accessory'?initialCategory:''));
    if(['short','medium','long'].includes(params.get('length')))$$('input[name=length]').forEach(r=>r.checked=r.value===params.get('length'));
    $('#recording-filter').checked=params.get('recording')==='1';
    if(['price-asc','price-desc','length'].includes(params.get('sort')))$('#sort-products').value=params.get('sort');
    function filter(updateURL=true){
      const options={query:queryInput.value.trim(),category:$('input[name=category]:checked')?.value||'',length:$('input[name=length]:checked')?.value||'',recording:$('#recording-filter').checked,sort:$('#sort-products').value};
      const list=core.filterProducts(products,options);grid.replaceChildren(...list.map(p=>cards.get(p.id)));$('#result-count').textContent=fa(list.length)+' محصول از '+fa(products.length)+' محصول';$('#shop-empty').hidden=!!list.length;$('#search-summary').hidden=!options.query;$('#search-summary').textContent='نتیجه جست‌وجو برای «'+options.query+'»';
      const categoryLabel=categories.find(c=>c.id===options.category)?.label||(options.category==='accessory'?'قطعات و لوازم جانبی':'همه تجهیزات بازرسی');
      $('#catalog-title').textContent=categoryLabel;
      $$('[data-shop-category]').forEach(a=>{if(a.dataset.shopCategory===options.category)a.setAttribute('aria-current','true');else a.removeAttribute('aria-current');});
      const chips=[];
      if(options.category)chips.push(['category',categoryLabel]);
      if(options.length)chips.push(['length',({short:'کابل تا ۵ متر',medium:'کابل ۵ تا ۱۵ متر',long:'کابل بیشتر از ۱۵ متر'})[options.length]]);
      if(options.recording)chips.push(['recording','ضبط تصویر']);
      if(options.query)chips.push(['query','جست‌وجو: '+options.query]);
      $('#active-filters').hidden=!chips.length;
      $('#active-filters').innerHTML=chips.map(([key,label])=>`<button data-remove-filter="${key}" aria-label="حذف فیلتر ${escape(label)}">${escape(label)} <span aria-hidden="true">×</span></button>`).join('')+(chips.length?'<button class="clear-all" data-reset-filters>حذف همه</button>':'');
      $('[data-filter-count]').textContent=fa(chips.length);$('[data-filter-count]').hidden=!chips.length;
      $('[data-filter-results]').textContent=fa(list.length);
      if(updateURL){const next=new URLSearchParams();if(options.query)next.set('q',options.query);if(options.category)next.set('category',options.category);if(options.length)next.set('length',options.length);if(options.recording)next.set('recording','1');if(options.sort!=='default')next.set('sort',options.sort);try{history.replaceState(null,'',location.pathname+(next.size?'?'+next.toString():''));}catch{/* File previews may restrict history updates. */}}
      sync();
    }
    $('#shop-filters').addEventListener('change',()=>filter());$('#sort-products').addEventListener('change',()=>filter());
    $('.header-search').addEventListener('submit',e=>{e.preventDefault();filter();});
    queryInput.addEventListener('input',()=>filter());
    document.addEventListener('click',event=>{
      const reset=event.target.closest('[data-reset-filters]'),remove=event.target.closest('[data-remove-filter]'),category=event.target.closest('[data-shop-category]');
      if(reset){queryInput.value='';$('input[name=category][value=""]').checked=true;$('input[name=length][value=""]').checked=true;$('#recording-filter').checked=false;$('#sort-products').value='default';filter();}
      if(remove){const key=remove.dataset.removeFilter;if(key==='query')queryInput.value='';else if(key==='recording')$('#recording-filter').checked=false;else $(`input[name=${key}][value=""]`).checked=true;filter();const focusTarget=$('#active-filters button')||$('#sort-products');focusTarget.focus({preventScroll:true});}
      if(category&&!event.ctrlKey&&!event.metaKey&&!event.shiftKey&&!event.altKey){event.preventDefault();$$('input[name=category]').forEach(r=>r.checked=r.value===category.dataset.shopCategory);filter();}
    });
    const filterDialog=$('#filter-dialog'),filterPanel=$('#shop-filters'),toggle=$('.filter-toggle');
    function closeFilters(){if(filterDialog.open)filterDialog.close();}
    toggle.addEventListener('click',()=>{$('#filter-dialog-body').append(filterPanel);filterDialog.showModal();toggle.setAttribute('aria-expanded','true');});
    filterDialog.addEventListener('close',()=>{$('#filter-home').append(filterPanel);toggle.setAttribute('aria-expanded','false');if(matchMedia('(max-width:900px)').matches)toggle.focus({preventScroll:true});});
    $('[data-close-filters]').addEventListener('click',closeFilters);
    $('[data-apply-filters]').addEventListener('click',closeFilters);
    filterDialog.addEventListener('click',event=>{if(event.target===filterDialog){const rect=filterDialog.getBoundingClientRect();if(event.clientX<rect.left||event.clientX>rect.right||event.clientY<rect.top||event.clientY>rect.bottom)closeFilters();}});
    matchMedia('(min-width:901px)').addEventListener('change',event=>{if(event.matches)closeFilters();});
    filter(false);
  }
  if($('#quiz-panel')){
    const panel=$('#quiz-panel');let step=0;const answers={};
    const questions=[{key:'category',title:'بیشتر برای چه کاری نیاز دارید؟',hint:'کاربرد اصلی، اولین معیار محدود کردن گزینه‌هاست.',options:[...categories.map(c=>[c.id,c.label]),['','هنوز مطمئن نیستم']]},{key:'length',title:'مسیر شما تقریباً چقدر طول دارد؟',hint:'دستگاه باید حداقل طول مورد نیاز شما را پوشش دهد.',options:[['5','تا ۵ متر'],['10','تا ۱۰ متر'],['20','تا ۲۰ متر'],['30','بیش از ۲۰ متر'],['','هنوز اندازه نگرفته‌ام']]},{key:'diameter',title:'قطر ورودی مسیر چقدر است؟',hint:'اندازه‌های این راهنما فقط برای غربال اولیه‌اند؛ فضای عبور و خمش باید جداگانه بررسی شود.',options:[['5','حداکثر ۵ میلی‌متر'],['8','حداکثر ۸ میلی‌متر'],['20','تا ۲۰ میلی‌متر'],['','بزرگ‌تر یا نامشخص']]},{key:'budget',title:'بودجه تقریبی شما چقدر است؟',hint:'مقایسه بر اساس قیمت نمونه کاتالوگ انجام می‌شود.',options:[['7000000','تا ۷ میلیون تومان'],['10000000','تا ۱۰ میلیون تومان'],['15000000','تا ۱۵ میلیون تومان'],['','فعلاً محدودیتی تعیین نمی‌کنم']]}];
    function render(moveFocus=false){
      if(step<questions.length){const q=questions[step];panel.innerHTML=`<div class="quiz-progress-label"><span>مرحله ${fa(step+1)} از ${fa(questions.length)}</span><span>راهنمای انتخاب</span></div><div class="progress" role="progressbar" aria-label="پیشرفت راهنمای انتخاب" aria-valuemin="0" aria-valuemax="4" aria-valuenow="${step+1}"><span style="width:${(step+1)*25}%"></span></div><h2 tabindex="-1">${q.title}</h2><p>${q.hint}</p><div class="quiz-options">${q.options.map(([value,label])=>`<button class="quiz-option" data-answer="${value}" aria-pressed="${answers[q.key]===value}">${label}</button>`).join('')}</div>${step?'<button class="link-button quiz-back" data-quiz-back>بازگشت به سؤال قبل</button>':''}`;}
      else{const results=core.recommend(products,answers);const chosen=questions.map(q=>q.options.find(([v])=>v===answers[q.key])?.[1]).filter(Boolean);panel.innerHTML=`<span class="eyebrow">نتیجه بررسی اولیه</span><h2 tabindex="-1">${results.length?'گزینه‌های مرتبط با پاسخ‌های شما':'گزینه منطبق پیدا نشد'}</h2><p class="quiz-explanation">${chosen.join(' • ')}</p>${results.length?`<p>این مدل‌ها شرط کاربرد، حداقل طول کابل، قطر هد و سقف بودجه انتخابی شما را در داده‌های نمونه دارند.</p><div class="quiz-result-list">${results.map(p=>`<article class="quiz-result-item"><img src="assets/images/${p.image}" alt="${p.name}" width="90" height="90"><div><h3>${p.name}</h3><p>کابل ${fa(p.length)} متر • هد ${fa(p.diameter)} میلی‌متر • ${fa(p.price)} تومان</p><a class="text-link" href="${p.slug}">بررسی مشخصات و محدودیت‌ها ${icon('arrow')}</a></div></article>`).join('')}</div>`:'<p>هیچ دستگاه کاملی در کاتالوگ نمونه تمام شرایط شما را ندارد. پاسخ‌ها را بازبینی کنید یا نیازتان را برای بررسی فنی آماده کنید.</p><a class="btn btn-outline" href="contact.html">آماده‌سازی درخواست بررسی</a>'}<p class="small-muted">این نتیجه تأیید تناسب فنی نیست؛ شرایط محیط و اطلاعات سازنده باید بررسی شوند.</p><button class="link-button quiz-back" data-quiz-back>ویرایش بودجه</button> <button class="link-button quiz-back" data-quiz-restart>شروع دوباره</button>`;}
      if(moveFocus)$('h2',panel).focus({preventScroll:true});
    }
    panel.addEventListener('click',event=>{const answer=event.target.closest('[data-answer]');if(answer){answers[questions[step].key]=answer.dataset.answer;step++;render(true);}if(event.target.closest('[data-quiz-back]')){step=Math.max(0,step-1);render(true);}if(event.target.closest('[data-quiz-restart]')){step=0;Object.keys(answers).forEach(k=>delete answers[k]);render(true);}});render();
  }
  $$('[data-request]').forEach(form=>{
    const phone=$('[name=phone]',form),status=$('.form-status',form),output=$('.request-output',form),text=$('#request-text',form);
    const product=products.find(p=>p.id===params.get('product'));
    if(product)$('[name=details]',form).value='مدل مورد نظر: '+product.name+'\n';
    if($('[name=topic]',form)&&['video','product','consult'].includes(params.get('topic')))$('[name=topic]',form).value=params.get('topic');
    const date=$('[name=date]',form);if(date){const now=new Date();date.min=[now.getFullYear(),String(now.getMonth()+1).padStart(2,'0'),String(now.getDate()).padStart(2,'0')].join('-');}
    phone.addEventListener('input',()=>{phone.setCustomValidity('');phone.removeAttribute('aria-invalid');});
    form.addEventListener('input',event=>{if(!event.target.closest('.request-output')){output.hidden=true;status.textContent='';}});
    form.addEventListener('change',event=>{if(!event.target.closest('.request-output')){output.hidden=true;status.textContent='';}});
    form.addEventListener('submit',event=>{
      event.preventDefault();const normalized=core.phone(phone.value);
      if(!/^09\d{9}$/.test(normalized)){phone.setCustomValidity('شماره موبایل معتبر وارد کنید؛ مانند ۰۹۱۲۱۲۳۴۵۶۷.');phone.setAttribute('aria-invalid','true');phone.reportValidity();status.textContent='شماره موبایل را بررسی کنید.';return;}
      phone.value=normalized;
      const data=new FormData(form),labels={name:'نام',phone:'شماره تماس',city:'شهر',date:'تاریخ شروع',days:'تعداد روز',model:'مدل دستگاه',details:'شرح درخواست'};
      const lines=['پیش‌نویس درخواست — ارسال نشده',form.dataset.request==='rent'?'موضوع: بررسی امکان اجاره':form.dataset.request==='repairs'?'موضوع: بررسی و تعمیر دستگاه':'موضوع: '+($('[name=topic] option:checked',form)?.textContent||'مشاوره')];
      for(const [key,label]of Object.entries(labels)){if(data.get(key))lines.push(label+': '+String(data.get(key)).trim());}
      text.value=lines.join('\n');output.hidden=false;status.textContent='متن آماده شد؛ هیچ اطلاعاتی ارسال نشده است.';text.focus();
    });
    $('[data-copy-request]',form).addEventListener('click',async()=>{try{await navigator.clipboard.writeText(text.value);status.textContent='متن درخواست کپی شد؛ هنوز ارسال نشده است.';}catch{text.focus();text.select();status.textContent='متن انتخاب شد. با گزینه کپی مرورگر یا Ctrl+C آن را کپی کنید.';}});
  });
  $('#download-order')?.addEventListener('click',()=>{if(!cart.length)return;const lines=['شلنگ‌بین — خلاصه انتخاب‌های نمونه','این فایل سفارش یا فاکتور نیست؛ قیمت و موجودی تأیید نشده است.','',...cart.map(row=>{const p=products.find(p=>p.id===row.id);return `${p.name} | تعداد: ${fa(row.qty)} | جمع: ${fa(p.price*row.qty)} تومان`;}),'','جمع نمونه: '+fa(cartTotal())+' تومان','هزینه ارسال تعیین نشده است.'];const url=URL.createObjectURL(new Blob(['\ufeff'+lines.join('\n')],{type:'text/plain;charset=utf-8'}));const a=document.createElement('a');a.href=url;a.download='shalangbin-selection.txt';document.body.append(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(url),1000);$('#checkout-status').textContent='دریافت فایل خلاصه آغاز شد. سفارشی ثبت نشده است.';});
  window.addEventListener('storage',event=>{if(event.key===cartKey||event.key===compareKey||event.key===null){if(event.key===cartKey||event.key===null)cartUndo=null;cart=core.cleanCart(read(cartKey,[]),products);comparison=cleanCompare(read(compareKey,[]));renderCart();renderComparison();sync();}});
  const lightbox=$('.pdp-lightbox');
  if(lightbox){
    $$('[data-open-image]').forEach(button=>button.addEventListener('click',()=>lightbox.showModal()));
    $('[data-close-image]',lightbox).addEventListener('click',()=>lightbox.close());
    lightbox.addEventListener('click',event=>{if(event.target===lightbox)lightbox.close();});
  }
  renderCart();renderComparison();sync();
})();
