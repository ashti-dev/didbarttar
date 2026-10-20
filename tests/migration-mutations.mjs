// Isolated regressions of source tripwires and the real navigation script; NOT PHP/browser tests.
import assert from 'node:assert/strict';
import {cpSync,mkdtempSync,readFileSync,writeFileSync,rmSync} from 'node:fs';
import {tmpdir} from 'node:os';
import {dirname,join,resolve} from 'node:path';
import {fileURLToPath} from 'node:url';
import {spawnSync} from 'node:child_process';
const root=resolve(dirname(fileURLToPath(import.meta.url)),'..');
const run=(file,args=[])=>spawnSync(process.execPath,[join(root,'tests',file),...args],{cwd:root,encoding:'utf8',timeout:60000});
for(const runner of ['migration-static.mjs','migration-navigation.mjs']){
 const result=run(runner);assert.equal(result.status,0,'Baseline must be green: '+result.stdout+result.stderr);
}
const mutations=[
 ['duplicate-TOC','inc/presentation.php','return array_values( $unique );','return $links;','M-S20'],
 ['unsafe-fragment','single.php',"rawurlencode( $torantejarat_link['id'] )","$torantejarat_link['id']",'M-S20'],
 ['empty-grid','template-parts/editorial-list.php',"if ( '' === trim( $torantejarat_result ) ) { continue; }",'', 'M-S21'],
 ['lost-pagination','template-parts/editorial-list.php','the_posts_pagination();','', 'M-S21'],
 ['cart-wrapper-order','inc/woocommerce.php',"torantejarat_cart_layout_end', 1","torantejarat_cart_layout_end', 99",'M-S22'],
 ['removed-totals','inc/woocommerce.php',null,"\nremove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );\n",'M-S22'],
 ['cart-business-write','inc/woocommerce.php',null,'\nWC()->cart->set_quantity( 1, 2 );\n','M-S22'],
 ['account-auth-write','inc/woocommerce.php',null,'\nwp_set_auth_cookie( 1 );\n','M-S23'],
 ['cart-columns','assets/css/native.css','grid-template-columns:minmax(0,1fr) 350px','grid-template-columns:minmax(0,1fr) 400px','M-S22'],
 ['duplicate-CSS','assets/css/native.css',null,'\n.torantejarat-native-cart-layout{gap:40px}\n','M-S19'],
 ['account-current-state','assets/css/native.css','a[aria-current="page"]','a[data-current="page"]','M-S23'],
];
const temp=mkdtempSync(join(tmpdir(),'torantejarat-mutations-'));
let detected=0;
try{
 for(const [name,file,from,to,expected] of mutations){
  const theme=join(temp,name);cpSync(join(root,'theme/torantejarat'),theme,{recursive:true});
  const path=join(theme,file),text=readFileSync(path,'utf8');
  if(from!==null)assert.ok(text.includes(from),'Mutation target absent: '+name);
  writeFileSync(path,from===null?text+to:text.replace(from,to));
  const result=run('migration-static.mjs',[theme]);
  assert.equal(result.status,1,'Mutation did not fail: '+name+' '+result.stderr);
  assert.ok(result.stderr.includes('FAIL '+expected),name+' '+result.stderr);
  detected++;console.log(`DETECTED ${name} → ${expected}`);
 }
 const script=readFileSync(join(root,'theme/torantejarat/assets/js/navigation.js'),'utf8');
 for(const [name,from,to,lastPassed,blocked] of [
  ['late-focus','setOpen(nav.contains(document.activeElement));','setOpen(false);','N11','N12'],
  ['escape-focus','close(true);','close(false);','N03','N04'],
 ]){
  assert.ok(script.includes(from));const path=join(temp,name+'.js');writeFileSync(path,script.replace(from,to));
  const result=run('migration-navigation.mjs',[path]);assert.equal(result.status,1);
  assert.ok(result.stdout.includes('PASS '+lastPassed)&&!result.stdout.includes('PASS '+blocked)&&result.stderr.includes('AssertionError'));
  detected++;console.log(`DETECTED ${name} → ${blocked}`);
 }
}finally{rmSync(temp,{recursive:true,force:true});}
console.log(`MUTATIONS: ${detected}/13 detected; temporary copies removed; production untouched.`);
