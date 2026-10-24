// Exercises the actual extracted script in a minimal DOM double, NOT a browser/a11y test.
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';
const code=readFileSync(process.argv[2] || new URL('../theme/torantejarat/assets/js/navigation.js',import.meta.url),'utf8');
function fixture(missing=false, focused=false){
 const listeners=new Map(), media={}, classes=new Set(),bodyClasses=new Set(),attrs=new Map();
 const node=(name)=>({name,addEventListener:(type,fn)=>listeners.set(`${name}:${type}`,fn)});
 const button={...node('button'),hidden:true,dataset:{openLabel:'open',closeLabel:'close'},setAttribute:(k,v)=>attrs.set(k,v),focus:()=>document.activeElement=button};
 const link={closest:()=>link};
 const nav={...node('nav'),classList:{add:c=>classes.add(c),remove:c=>classes.delete(c),contains:c=>classes.has(c),toggle:(c,on)=>on?classes.add(c):classes.delete(c)},contains:n=>n===link};
 const header={...node('header'),contains:n=>n===button||n===link};
 const document={...node('document'),body:{classList:{add:c=>bodyClasses.add(c)}},activeElement:focused?link:null,querySelector:s=>missing?null:s.includes('menu-toggle')?button:s.includes('main-nav')?nav:header};
 const fire=(target,type,event={})=>{assert.ok(listeners.has(`${target}:${type}`));listeners.get(`${target}:${type}`)(event);};
 vm.runInNewContext(code,{document,matchMedia:q=>{assert.equal(q,'(min-width:901px)');return{addEventListener:(t,fn)=>media[t]=fn};},setTimeout:fn=>fn()});
 return{button,nav,header,document,link,attrs,classes,bodyClasses,media,fire,listeners};
}
let count=0;
function test(name,fn){fn();count++;console.log(`PASS ${name}`);}
test('N01-missing-menu-safe',()=>assert.equal(fixture(true).listeners.size,0));
test('N02-progressive-enhancement',()=>{const f=fixture();assert.equal(f.button.hidden,false);assert.ok(f.bodyClasses.has('torantejarat-js'));});
test('N03-toggle-label-and-expanded',()=>{const f=fixture();f.fire('button','click');assert.equal(f.attrs.get('aria-expanded'),'true');assert.equal(f.attrs.get('aria-label'),'close');assert.ok(f.classes.has('torantejarat-is-open'));f.fire('button','click');assert.equal(f.attrs.get('aria-expanded'),'false');assert.equal(f.attrs.get('aria-label'),'open');});
test('N04-escape-restores-focus',()=>{const f=fixture();f.fire('button','click');f.fire('document','keydown',{key:'Escape'});assert.equal(f.document.activeElement,f.button);assert.equal(f.attrs.get('aria-expanded'),'false');});
test('N05-outside-click-closes',()=>{const f=fixture();f.fire('button','click');f.document.activeElement=f.link;f.fire('document','click',{target:{}});assert.equal(f.attrs.get('aria-expanded'),'false');assert.equal(f.document.activeElement,f.button);});
test('N06-inside-click-does-not-close',()=>{const f=fixture();f.fire('button','click');f.fire('document','click',{target:f.button});assert.equal(f.attrs.get('aria-expanded'),'true');});
test('N07-link-selection-closes',()=>{const f=fixture();f.fire('button','click');f.fire('nav','click',{target:f.link});assert.equal(f.attrs.get('aria-expanded'),'false');});
test('N08-desktop-transition-closes',()=>{const f=fixture();f.fire('button','click');f.media.change({matches:true});assert.equal(f.attrs.get('aria-expanded'),'false');});
test('N09-focus-leaving-header-closes',()=>{const f=fixture();f.fire('button','click');f.document.activeElement={};f.fire('header','focusout');assert.equal(f.attrs.get('aria-expanded'),'false');});
test('N10-link-does-not-leave-focus-hidden',()=>{const f=fixture();f.fire('button','click');f.document.activeElement=f.link;f.fire('nav','click',{target:f.link});assert.equal(f.document.activeElement,f.button);});
test('N11-mobile-transition-does-not-hide-focused-link',()=>{const f=fixture();f.document.activeElement=f.link;f.media.change({matches:false});assert.equal(f.document.activeElement,f.button);assert.equal(f.attrs.get('aria-expanded'),'false');});
test('N12-late-script-preserves-existing-nav-focus',()=>{const f=fixture(false,true);assert.equal(f.document.activeElement,f.link);assert.equal(f.attrs.get('aria-expanded'),'true');assert.ok(f.classes.has('torantejarat-is-open'));});
test('N13-other-key-does-not-close-menu',()=>{const f=fixture();f.fire('button','click');f.fire('document','keydown',{key:'ArrowDown'});assert.equal(f.attrs.get('aria-expanded'),'true');});
console.log(`NAVIGATION UNIT CHECKS: ${count}/${count}; DOM double, not browser evidence.`);
