(function(root){
  const normalizeDigits=value=>String(value).replace(/[۰-۹]/g,c=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(c)).replace(/[٠-٩]/g,c=>'٠١٢٣٤٥٦٧٨٩'.indexOf(c));
  const normalize=value=>normalizeDigits(value).toLowerCase().replace(/ي/g,'ی').replace(/ك/g,'ک').replace(/[\u200c\s]+/g,' ').trim();
  const phone=value=>normalizeDigits(value).replace(/[\s()-]/g,'').replace(/^\+98/,'0').replace(/^0098/,'0');
  function filterProducts(products,{query='',category='',length='',recording=false,sort='default'}={}){
    let result=products.filter(p=>(!category||p.categories.includes(category))&&(!query||normalize(p.name+' '+p.en+' '+p.tag+' '+p.length+' '+p.diameter+' '+p.resolution+' '+p.waterproof).includes(normalize(query)))&&(!length||(length==='short'?p.length<=5:length==='medium'?p.length>5&&p.length<=15:p.length>15))&&(!recording||p.recording));
    if(sort==='price-asc')result.sort((a,b)=>a.price-b.price);
    if(sort==='price-desc')result.sort((a,b)=>b.price-a.price);
    if(sort==='length')result.sort((a,b)=>b.length-a.length);
    return result;
  }
  function cleanCart(value,products){
    if(!Array.isArray(value))return [];
    const result=[];
    for(const row of value){if(!row||!products.some(p=>p.id===row.id)||!Number.isFinite(row.qty))continue;const qty=Math.min(99,Math.max(0,Math.floor(row.qty)));if(!qty)continue;const old=result.find(x=>x.id===row.id);if(old)old.qty=Math.min(99,old.qty+qty);else result.push({id:row.id,qty});}
    return result;
  }
  function recommend(products,answers){
    return products.filter(p=>!p.categories.includes('accessory')&&(!answers.category||p.categories.includes(answers.category))&&(!answers.length||p.length>=Number(answers.length))&&(!answers.diameter||p.diameter<=Number(answers.diameter))&&(!answers.budget||p.price<=Number(answers.budget))).sort((a,b)=>a.price-b.price);
  }
  const api={normalizeDigits,normalize,phone,filterProducts,cleanCart,recommend};
  if(typeof module!=='undefined'&&module.exports)module.exports=api;else root.ShalangCore=api;
})(typeof window!=='undefined'?window:globalThis);
