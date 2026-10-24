import assert from 'node:assert/strict';
import {createHash} from 'node:crypto';
import {existsSync,readFileSync,readdirSync} from 'node:fs';
import {dirname,join,relative,resolve} from 'node:path';
import {fileURLToPath} from 'node:url';
const root=resolve(dirname(fileURLToPath(import.meta.url)),'..');
const theme=resolve(process.argv[2]||join(root,'theme/torantejarat'));
const walk=dir=>readdirSync(dir,{withFileTypes:true}).flatMap(e=>e.name==='.git'?[]:e.isDirectory()?walk(join(dir,e.name)):[join(dir,e.name)]);
const read=n=>readFileSync(join(theme,n),'utf8');
const hash=p=>createHash('sha256').update(readFileSync(p)).digest('hex');
const files=walk(theme), php=files.filter(f=>f.endsWith('.php'));
const source=php.map(f=>readFileSync(f,'utf8')).join('\n');
const baseline=JSON.parse(readFileSync(join(root,'tests/fixtures/migration-baseline.json')));
let failed=0,total=0;
function check(name,run){total++;try{run();console.log(`PASS ${name}`);}catch(e){failed++;console.error(`FAIL ${name}: ${e.message}`);}}
const includes=(code,values)=>values.forEach(v=>assert.ok(code.includes(v),v));
check('M-S01-classic-hierarchy-and-identity',()=>{
 for(const n of ['header','footer','index','front-page','home','page','single','archive','search','404','searchform'])assert.ok(existsSync(join(theme,n+'.php')),n);
 includes(read('style.css'),['Theme Name: توران تجارت','Text Domain: torantejarat','Author: یعقوب طیبی','Designer: یعقوب طیبی','Author URI: https://yaghoubtayebi.ir/']);
 for(const n of ['templates','parts','theme.json','node_modules','vendor'])assert.ok(!existsSync(join(theme,n)),n);
 assert.ok(!/Tested up to:/i.test(read('style.css')));
});
check('M-S02-prefixes-direct-access-and-callbacks',()=>{
 const functions=[];
 for(const f of php){const code=readFileSync(f,'utf8');assert.match(code,/namespace ToranTejarat\\Theme;/,f);assert.match(code,/if \( ! defined\( 'ABSPATH' \) \) \{\s*exit;/,f);for(const [,n]of code.matchAll(/function\s+(\w+)\s*\(/g)){assert.ok(n.startsWith('torantejarat_'),n);functions.push(n);}}
 assert.equal(new Set(functions).size,functions.length);
 const callbacks=[...source.matchAll(/__NAMESPACE__\s*\.\s*'\\\\(torantejarat_\w+)'/g)];assert.ok(callbacks.length>=15,'Callback scanner exercised');
 callbacks.forEach(([,n])=>assert.ok(functions.includes(n),n));
 assert.ok(!/shalang|didbarttar|شلنگ/i.test(source));
 for(const [,d]of source.matchAll(/\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'[^']*'\s*,\s*'([^']+)'/g))assert.equal(d,'torantejarat');
});
check('M-S03-native-shell-content-and-safe-links',()=>{
 includes(read('header.php'),['language_attributes();','wp_head();','wp_body_open();','wp_nav_menu(','get_search_form();',"get_template_part( 'template-parts/brand' )"]);
 includes(read('template-parts/brand.php'),['esc_url( home_url(',"get_bloginfo( 'name' )"]);
 includes(read('template-parts/page-intro.php'),['esc_html( $torantejarat_intro_title )','esc_url( home_url(']);
 includes(read('footer.php'),['wp_footer();','get_privacy_policy_url()','wp_nav_menu(']);
 for(const n of ['page.php','single.php','page-templates/about.php','page-templates/request.php'])includes(read(n),['the_content();','wp_link_pages();']);
 includes(read('searchform.php'),['method="get"','name="s"','esc_attr( get_search_query( false ) )','wp_unique_id(']);
 includes(read('front-page.php'),["'posts' === get_option( 'show_on_front' )","get_template_part( 'home' )",'post_password_required()','the_post_thumbnail(']);
 includes(read('template-parts/editorial-card.php'),['get_the_excerpt()','the_permalink();','the_post_thumbnail(','is_visible()']);
 assert.ok(!/href="(?:[^"<]*\.html|#)"/.test(source));
});
check('M-S04-source-css-equivalence-and-media-integrity',()=>{
 for(const f of readdirSync(join(root,'assets/css')).filter(f=>f.endsWith('.css'))){
  const legacy=readFileSync(join(root,'assets/css',f),'utf8');
  const expected=legacy.replace(/\.([a-zA-Z_][\w-]*)/g,'.torantejarat-$1').replace(':root{','.torantejarat-site{');
  assert.equal(read('assets/css/source/'+f),expected,`Only namespace/token scope adaptation: ${f}`);
 }
 for(const folder of ['images','video'])for(const f of readdirSync(join(root,'assets',folder)))assert.equal(hash(join(theme,'assets',folder,f)),hash(join(root,'assets',folder,f)),f);
 includes(read('assets/css/native.css'),['max-width:1100px','max-width:900px','max-width:600px','torantejarat-site:not(.torantejarat-js)','[hidden]','focus-visible']);
});
check('M-S05-enqueue-and-conditional-assets',()=>{
 const a=read('inc/assets.php');
 includes(a,['wp_enqueue_style(','wp_enqueue_script(',"'torantejarat-source'","'torantejarat-native'",'is_shop() || is_product_taxonomy()','if ( is_product() )',"has_nav_menu( 'torantejarat-primary' )","wp_script_is( 'wc-cart-fragments', 'registered' )","'strategy' => 'defer'",'array_keys( $styles )','filemtime(']);
 assert.ok(!/torantejarat-(?:base|layout|components)'|source\/(?:cart|compare)\.css/.test(a),'No competing Foundation/mock-cart styles');
 for(const [,p]of a.matchAll(/'(assets\/(?:css|js)\/[^']+\.(?:css|js))'/g))assert.ok(existsSync(join(theme,p)),p);
 assert.equal((source.match(/wp_enqueue_style\(/g)||[]).length,1);
 assert.ok(!/<script\b|<link[^>]*stylesheet|fonts\.google/.test(source));
});
check('M-S06-native-Woo-loop-preserves-public-hooks',()=>{
 assert.deepEqual(readdirSync(join(theme,'woocommerce')),['content-product.php']);
 const card=read('woocommerce/content-product.php');
 includes(card,['instanceof \\WC_Product','is_visible()','wc_product_class(','get_sku()','get_short_description()','@version 9.4.0']);
 const hooks=['woocommerce_before_shop_loop_item','woocommerce_before_shop_loop_item_title','woocommerce_shop_loop_item_title','woocommerce_after_shop_loop_item_title','woocommerce_after_shop_loop_item'];
 let last=-1;for(const h of hooks){const i=card.indexOf(`do_action( '${h}' )`);assert.ok(i>last,h);last=i;}
 assert.ok(!/get_price\(|[\d,]+ تومان|data-product-id=|<form/.test(card));
});
check('M-S07-native-Woo-single-and-cart-boundaries',()=>{
 const w=read('inc/woocommerce.php');
 includes(w,["class_exists( 'WooCommerce', false )","current_user_can( 'activate_plugins' )","'wc-product-gallery-slider'","'wc-product-gallery-lightbox'",'woocommerce_before_single_product_summary','woocommerce_after_single_product_summary','woocommerce_single_product_summary','woocommerce_add_to_cart_fragments','get_cart_contents_count()','wc_get_cart_url()']);
 assert.ok(!/woocommerce\/cart|woocommerce\/checkout|process_payment|payment_complete|WC_Order|wp_remote_|register_rest_route/.test(source));
 includes(read('page.php'),['the_content();']);
 assert.ok(!/remove_action\(\s*'woocommerce_single_product_summary',\s*'woocommerce_template_single_add_to_cart'/.test(w));
 assert.ok(!/remove_action\([^\n]+(?:woocommerce_output_product_data_tabs|woocommerce_show_product_images)/.test(w));
});
check('M-S08-bounded-native-queries-and-reset',()=>{
 includes(read('template-parts/home-posts.php'),["'posts_per_page' => 3","'no_found_rows' => true",'wp_reset_postdata()']);
 includes(read('template-parts/home-products.php'),['wc_get_products(',"'featured' => true","'visibility' => 'catalog'","'limit' => 4",'wc_setup_loop(','wc_reset_loop()','wp_reset_postdata()']);
 includes(read('inc/presentation.php'),["'core/heading' === $block['blockName']","$block['attrs']['anchor']",'wp_strip_all_tags(']);
});
check('M-S09-no-unapproved-schema-handlers-or-authoritative-data',()=>{
 assert.ok(!/\b(?:register_setting|register_(?:post_)?meta|register_post_type|register_taxonomy|(?:add|update|delete)_(?:option|post_meta|user_meta)|wp_insert_post|dbDelta|curl_\w+)\s*\(/.test(source));
 assert.ok(!/\$wpdb|acf_|elementor|register_rest_route|wp_ajax_|admin_post_/.test(source));
 assert.ok(!/<form|type="submit"|data-request=/.test(read('page-templates/request.php')),'Request page has no fake submission');
 const js=files.filter(f=>f.endsWith('.js'));assert.equal(js.length,1);assert.ok(js[0].endsWith('/navigation.js'));
 assert.ok(!/localStorage|sessionStorage|fetch\(|XMLHttpRequest|catalog\.js/.test(js.map(f=>readFileSync(f,'utf8')).join('')));
});
check('M-S10-retained-reference-and-historical-QA',()=>{
 let protectedCount=0;
 const allowedDocs=new Set(['README.md','tests/README.md',...['ACCEPTANCE','ADR','AGENTS','ARCHITECTURE-CONTRACT','DEVELOPMENT-PROTOCOL','FINAL-DECISION-SHEET','QA'].map(n=>`wp-theme-agent-kit/${n}.md`)]);
 const additions=new Set(['tests/fixtures/migration-baseline.json','tests/migration-static.mjs','tests/migration-navigation.mjs','tests/migration-mutations.mjs','tests/migration-smoke.php','wp-theme-agent-kit/PHASE-6-MIGRATION-MAP.md','wp-theme-agent-kit/PHASE-6-MIGRATION-REPORT.md']);
 for(const file of walk(root)) { const name=relative(root,file);assert.ok(Object.hasOwn(baseline.files,name)||name.startsWith('theme/torantejarat/')||additions.has(name),`Unapproved addition: ${name}`); }
 for(const [name,digest]of Object.entries(baseline.files)){
  if(name.startsWith('theme/torantejarat/')||allowedDocs.has(name))continue;
  assert.equal(hash(join(root,name)),digest,name);protectedCount++;
 }
 assert.ok(protectedCount>=39,'Reference and historical QA inventory covered');
});
check('M-S11-no-new-runtime-dependency-or-remote-assets',()=>{
 for(const f of files.filter(f=>/\.(?:php|css|js)$/.test(f))){const text=readFileSync(f,'utf8').replaceAll('https://yaghoubtayebi.ir/','');assert.ok(!/https?:\/\/|@import|localStorage|ShalangCatalog/.test(text),relative(theme,f));}
 for(const n of ['package.json','package-lock.json','composer.json','composer.lock'])assert.ok(!existsSync(join(root,n)),n);
});
check('M-S12-template-branch-shape-not-PHP-lint',()=>{
 // A source tripwire for dangling alternative template branches, NOT a PHP parser.
 for(const f of php){
  let code=[...readFileSync(f,'utf8').matchAll(/<\?php([\s\S]*?)(?:\?>|$)/g)].map(m=>m[1]).join('\n');
  code=code.replace(/<<<'HTML'\n[\s\S]*?\nHTML/g,'NULL').replace(/\/\*[\s\S]*?\*\/|\/\/[^\n]*|'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"/g,' ');
  const events=[];
  for(const m of code.matchAll(/\b(if|foreach|while|for)\s*\(/g)){
   let i=m.index+m[0].length,depth=1;
   while(i<code.length&&depth){if(code[i]==='(')depth++;if(code[i]===')')depth--;i++;}
   if(code.slice(i).trimStart().startsWith(':'))events.push({i:m.index,type:m[1],open:true});
  }
  for(const m of code.matchAll(/\b(endif|endforeach|endwhile|endfor)\b/g))events.push({i:m.index,type:m[1].slice(3),open:false});
  const stack=[];
  for(const event of events.sort((a,b)=>a.i-b.i)){if(event.open)stack.push(event.type);else assert.equal(stack.pop(),event.type,relative(theme,f));}
  assert.equal(stack.length,0,relative(theme,f));
 }
});
check('M-S13-native-Home-patterns-and-source-order',()=>{
 const patterns=read('inc/patterns.php');const html=[...patterns.matchAll(/'content' => <<<'HTML'\n([\s\S]*?)\nHTML/g)].map(m=>m[1]);assert.equal(html.length,3);
 for(const content of html){
  const stack=[];
  for(const [,closing,name,attrs]of content.matchAll(/<!-- (\/?)wp:([\w/-]+)(.*?) -->/g)){
   assert.ok(['group','paragraph','heading','video','details'].includes(name),name);
   if(closing)assert.equal(stack.pop(),name);else{stack.push(name);if(attrs.trim())JSON.parse(attrs);}
  }
  assert.equal(stack.length,0);assert.equal(content.replace(/<!--[\s\S]*?-->|<[^>]*>/g,'').trim(),'','No seeded business copy');
  assert.ok(!/src=|href=|data-product|<form/.test(content),'No demo media, fake links or forms');
 }
 includes(read('inc/presentation.php'),['serialize_blocks( $blocks )',"apply_filters( 'the_content', $content )","'core/group' === $block['blockName']",'post_password_required()']);
 const home=read('front-page.php');let previous=-1;
 for(const needle of ["$torantejarat_home_regions['benefits']","get_template_part( 'template-parts/home-products' )","$torantejarat_home_regions['body']","get_template_part( 'template-parts/home-services' )","get_template_part( 'template-parts/home-posts' )","$torantejarat_home_regions['faq']"]){const i=home.indexOf(needle);assert.ok(i>previous,needle);previous=i;}
 includes(home,['if ( $torantejarat_category_html )']);
 assert.ok(home.indexOf("$torantejarat_home_regions['faq']")<home.indexOf('endwhile;'),'All home regions render in the main loop');
});
check('M-S14-Woo-visible-attributes-and-source-layout',()=>{
 const woo=read('inc/woocommerce.php');includes(woo,['instanceof \\WC_Product_Attribute','get_visible()','post_password_required( $product->get_id() )','wc_attribute_label(','get_attribute(','woocommerce_shop_loop_header',"'woocommerce_template_single_meta', 2","$args['posts_per_page'] = 3",'torantejarat-catalog-toolbar','woocommerce_output_related_products();',"if ( '' === trim( $html ) )"]);
 const card=read('woocommerce/content-product.php');includes(card,['wc_get_stock_html( $product )','torantejarat-shop-card-specs','torantejarat-spec-chips','! post_password_required( $product->get_id() ) && $product->get_short_description()']);
 assert.ok(!/get_post_meta\(|pa_|get_length\(|get_width\(/.test(woo),'No custom schema or guessed attribute/dimension mapping');
 const css=read('assets/css/native.css');includes(css,['max-width:760px','width:67px','width:54px','aspect-ratio:1','woocommerce-tabs ul.tabs','table.shop_attributes','price del','price ins','woocommerce-product-gallery__image--placeholder','.post-password-form']);
});
check('M-S15-existing-content-Academy-and-service-links',()=>{
 const helper=read('inc/presentation.php');includes(helper,['wp_get_nav_menu_items(','is_post_publicly_viewable( $content )','post_password_required( $content )',"array( 'post', 'page' )"]);
 includes(read('page-templates/academy.php'),['torantejarat_content_menu_items(',"get_template_part( 'template-parts/editorial-card', null, array( 'heading_level' => 3 ) )",'the_content();','wp_reset_postdata();','post_password_required()']);
 includes(read('template-parts/home-services.php'),['torantejarat-service-grid','torantejarat-service-card','get_permalink(','post_excerpt']);
 for(const n of ['about','request'])includes(read(`page-templates/${n}.php`),['has_excerpt() || has_post_thumbnail()','torantejarat-no-aside']);
});
check('M-S16-native-commerce-passthrough-and-empty-search',()=>{
 includes(read('page.php'),['torantejarat_is_commerce_page()','the_content();','wp_link_pages();']);
 includes(read('inc/woocommerce.php'),['is_cart() || is_checkout() || is_account_page()','esc_attr( $label )']);
 includes(read('search.php'),['get_search_form();',"get_template_part( 'template-parts/editorial-list' )"]);
 includes(read('template-parts/editorial-list.php'),['! $torantejarat_visible_results',"get_template_part( 'template-parts/content-none' )"]);
 const smoke=readFileSync(join(root,'tests/migration-smoke.php'),'utf8');
 includes(smoke,["array( 'cart', 'checkout', 'myaccount' )",'checkout-empty-cart-redirect','NOT TESTED checkout form']);
});
check('M-S17-empty-source-slots-and-native-FAQ',()=>{
 const p=read('inc/patterns.php');
 includes(p,['function torantejarat_render_content_slot(',"if ( is_admin() )",'array_intersect( $slots, $classes )',"'torantejarat-faq-item'",'preg_match(','$content_html = preg_replace(',"'torantejarat-video-panel'",'! $video_panel && torantejarat_slot_has_text( $content_html )','new \\WP_HTML_Tag_Processor(',"get_attribute( 'src' )",'is_string( $src )',"esc_url( trim( $src ) )",'ENT_QUOTES | ENT_HTML5','is_string( $class_name )']);
 includes(read('inc/bootstrap.php'),["'render_block_core/group'","'render_block_core/details'",'torantejarat_render_content_slot']);
 const region=read('inc/presentation.php');includes(region,["$html = apply_filters( 'the_content', $content )","if ( '' === trim( $html ) )",'is_string( $class_name )']);
 assert.ok(!/wp_update_post|update_post_meta|update_option/.test(p),'Pruning must not rewrite authored content');
});
check('M-S18-card-hierarchy-and-visible-featured-products',()=>{
 includes(read('template-parts/editorial-card.php'),["3 === ( $args['heading_level'] ?? 2 ) ? 'h3' : 'h2'",'esc_html( get_the_title() )']);
 for(const n of ['template-parts/home-posts.php','page-templates/academy.php'])includes(read(n),["'heading_level' => 3"]);
 const w=read('inc/woocommerce.php');includes(w,["array( 'torantejarat-home', 'related', 'up-sells', 'cross-sells' )","'woocommerce_product_loop_title_classes'",'esc_attr( $class )','esc_html( get_the_title() )']);
 const products=read('template-parts/home-products.php');includes(products,['$item->is_visible()',"'total' => count( $torantejarat_products )"]);
 assert.ok(products.indexOf('$item->is_visible()')<products.indexOf('if ( ! $torantejarat_products )'),'No empty featured section after visibility filtering');
});
// CSS structural tripwires and source-value comparisons, NOT a browser/CSS-engine test.
function cssRules(text,context=[]){
 text=text.replace(/\/\*[\s\S]*?\*\//g,'');let pos=0;const rules=[];
 while(text.slice(pos).trim()){
  const start=text.indexOf('{',pos);assert.ok(start>=0,'CSS opening brace');
  const selector=text.slice(pos,start).trim();assert.ok(selector&&!selector.includes('}'),'CSS selector');
  let i=start+1,depth=1,quote='';
  for(;i<text.length&&depth;i++){
   const c=text[i];if(quote){if(c==='\\'){i++;continue;}if(c===quote)quote='';continue;}
   if(c==='"'||c==="'"){quote=c;continue;}if(c==='{')depth++;if(c==='}')depth--;
  }
  assert.equal(depth,0,'CSS closing brace');assert.equal(quote,'','CSS quote');
  const body=text.slice(start+1,i-1);pos=i;
  if(selector.startsWith('@'))rules.push(...cssRules(body,[...context,selector.replace(/\s+/g,'')]));
  else{
   const properties={};for(const d of body.split(';').map(x=>x.trim()).filter(Boolean)){const colon=d.indexOf(':');assert.ok(colon>0,d);const key=d.slice(0,colon).trim();assert.ok(!Object.hasOwn(properties,key),'Repeated declaration '+key);properties[key]=d.slice(colon+1).trim();}
   rules.push({selector,properties,context:context.join('|')});
  }
 }
 return rules;
}
function cssSelectors(list){let depth=0,start=0;const parts=[];for(let i=0;i<list.length;i++){if(list[i]==='('||list[i]==='[')depth++;if(list[i]===')'||list[i]===']')depth--;if(list[i]===','&&depth===0){parts.push(list.slice(start,i).trim());start=i+1;}}parts.push(list.slice(start).trim());return parts;}
check('M-S19-CSS-structure-duplicates-and-source-parity',()=>{
 for(const f of files.filter(f=>f.endsWith('.css')))cssRules(readFileSync(f,'utf8'));
 const native=cssRules(read('assets/css/native.css'));const seen=new Set();
 for(const r of native)for(const sel of cssSelectors(r.selector))for(const prop of Object.keys(r.properties)){const key=[r.context,sel,prop].join('|');assert.ok(!seen.has(key),'Duplicate bridge declaration: '+key);seen.add(key);}
 const value=(rules,sel,prop,context='')=>rules.filter(r=>r.context===context&&cssSelectors(r.selector).includes(sel)&&Object.hasOwn(r.properties,prop)).at(-1)?.properties[prop];
 const sourceCss=cssRules(readFileSync(join(root,'assets/css/theme.css'),'utf8'));
 const pdp=cssRules(readFileSync(join(root,'assets/css/product.css'),'utf8'));
 const image='.torantejarat-site ul.products.torantejarat-product-grid .torantejarat-product-image img';
 assert.equal(value(native,image,'object-fit'),value(sourceCss,'.product-image img','object-fit'));
 const badge='.torantejarat-site ul.products.torantejarat-product-grid .onsale';
 for(const prop of ['font-size','padding','background','border-radius'])assert.equal(value(native,badge,prop),value(sourceCss,'.product-tag',prop),prop);
 const photoBadge='.torantejarat-site .torantejarat-pdp-gallery>.onsale';
 for(const prop of ['font-size','padding','background','border-radius'])assert.equal(value(native,photoBadge,prop),value(pdp,'.pdp-photo-tag',prop),prop);
 assert.equal(value(native,'.torantejarat-hero-actions a','font-size','@media(max-width:1150px)'),value(sourceCss,'.hero-actions .btn','font-size','@media(max-width:1150px)'));
 includes(read('assets/css/native.css'),['.torantejarat-editorial-card:focus-within',':is(h2,h3)',':has(>:only-child)']);
});
check('M-S20-article-native-TOC-and-source-sidebar',()=>{
 const helper=read('inc/presentation.php'),single=read('single.php');
 includes(helper,["'core/heading' === $block['blockName']",'is_string( $anchor )','html_entity_decode(',"$unique[ $link['id'] ]",'array_values( $unique )']);
 includes(single,['post_password_required() ? array()',"get_option( 'page_for_posts' )",'is_post_publicly_viewable( $torantejarat_blog_page )','aria-labelledby="torantejarat-toc-title"','<h2 id="torantejarat-toc-title"','<ul>','rawurlencode( $torantejarat_link[\'id\'] )','torantejarat-article-body','wp_link_pages();']);
 assert.ok(!/update_post|sanitize_title|wp_insert|add_post_meta/.test(helper),'TOC never rewrites content or invents anchors');
 const css=read('assets/css/native.css');includes(css,['.torantejarat-content-wide .torantejarat-prose{max-width:760px}','max-height:calc(100vh - 185px)','scroll-margin-top:185px','max-height:none;overflow:visible','.post-nav-links','.post-page-numbers']);
 assert.ok(!css.includes('.page-links'),'Default native WP pagination is post-nav-links, not the dead page-links bridge');
});
check('M-S21-main-query-listings-and-intentional-empty-states',()=>{
 for(const f of ['home.php','archive.php','index.php','search.php'])includes(read(f),["get_template_part( 'template-parts/editorial-list' )"]);
 const list=read('template-parts/editorial-list.php');includes(list,['while ( have_posts() )','the_post();',"get_template_part( 'template-parts/editorial-card' )","if ( '' === trim( $torantejarat_result ) ) { continue; }",'if ( ! $torantejarat_visible_results )',"get_template_part( 'template-parts/content-none' )",'the_posts_pagination();']);
 assert.ok(list.indexOf("if ( '' === trim(")<list.indexOf('torantejarat-editorial-grid'),'No empty grid before filtering');
 assert.ok(!/WP_Query|get_posts\(|query_posts\(/.test(list),'Do not replace the main query');
 includes(read('template-parts/content-none.php'),['is_search()',"esc_url( home_url( '/' ) )",'torantejarat-empty-state']);
 includes(read('404.php'),['get_search_form();',"esc_url( home_url( '/' ) )",'torantejarat-empty-state']);
});
check('M-S22-Cart-presentation-only-and-source-values',()=>{
 const w=read('inc/woocommerce.php');
 includes(w,["'woocommerce_before_cart', __NAMESPACE__ . '\\\\torantejarat_cart_layout_start', 99", "'woocommerce_after_cart', __NAMESPACE__ . '\\\\torantejarat_cart_layout_end', 1", "remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 )", "add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20 )",'torantejarat-native-cart-layout']);
 assert.ok(!/->(?:set_quantity|calculate_totals|add_to_cart|remove_cart_item|empty_cart)\s*\(|get_cross_sells\(|set_cart_contents\(/.test(source),'No Theme cart math, mutations or product selection');
 assert.ok(!/remove_action\([^\n]+(?:woocommerce_cart_totals|woocommerce_button_proceed_to_checkout|woocommerce_output_all_notices)/.test(w));
 const rules=cssRules(read('assets/css/native.css')),cart=cssRules(readFileSync(join(root,'assets/css/cart.css'),'utf8'));
 const value=(set,sel,p,ctx='')=>set.filter(r=>r.context===ctx&&cssSelectors(r.selector).includes(sel)&&Object.hasOwn(r.properties,p)).at(-1)?.properties[p];
 for(const prop of ['grid-template-columns','gap','align-items'])assert.equal(value(rules,'.torantejarat-native-cart-layout',prop),value(cart,'#cart-content .cart-layout',prop),prop);
 const totals='.torantejarat-site .torantejarat-native-cart-layout .cart_totals';
 for(const prop of ['background','border','padding','border-radius'])assert.equal(value(rules,totals,prop),value(cart,'#cart-content .summary-card',prop),prop);
 assert.equal(value(rules,'.torantejarat-native-cart-layout','grid-template-columns','@media(max-width:1000px)'),'1fr');
 includes(read('assets/css/native.css'),['.quantity input.qty','.product-thumbnail img','.cart-empty','.wc-proceed-to-checkout','min-height:52px',':where(a.button,button.button,input.button)']);
});
check('M-S23-Account-native-endpoints-and-shared-presentation',()=>{
 const css=read('assets/css/native.css');
 includes(css,['.woocommerce-MyAccount-navigation','.woocommerce-MyAccount-content','a[aria-current="page"]','.woocommerce-orders-table','.woocommerce-EditAccountForm','form.lost_reset_password','.form-row-first','grid-template-columns:190px minmax(0,1fr);gap:35px']);
 // Native account templates and nonce/logout endpoints are not replaced or reimplemented.
 assert.ok(!existsSync(join(theme,'woocommerce/myaccount')));
 assert.ok(!/\b(?:wp_signon|wp_set_auth_cookie|wp_authenticate|wp_logout|wp_create_user|wp_update_user|add_rewrite_endpoint)\s*\(/.test(source));
 assert.ok(!/woocommerce_account_menu_items|woocommerce_account_\w+_endpoint|woocommerce_save_account_details/.test(source));
 includes(read('page.php'),['the_content();','torantejarat_is_commerce_page()']);
});
console.log(`MIGRATION SOURCE CHECKS: ${total-failed}/${total}; PHP syntax, rendering and compatibility NOT assessed.`);
process.exitCode=failed?1:0;
