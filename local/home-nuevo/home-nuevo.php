<?php
/**
 * Template Name: Home Nuevo (Vitrina)
 * Description: Home de prueba (vitrina de ventas). No afecta al home actual.
 */
if (!defined('ABSPATH')) exit;

/* ---- Datos ---- */
// Combos del HERO (los que ya tienen imagen propia)
$sp_hero_ids = array(21516, 21517, 21518, 21519, 21520, 21521, 21960);
$sp_hero_img = array(
    21516 => '/wp-content/uploads/2026/09/combo-21516.png',
    21517 => '/wp-content/uploads/2026/09/combo-21517.png',
    21518 => '/wp-content/uploads/2026/09/combo-21518.png',
    21519 => '/wp-content/uploads/2026/09/combo-21519.png',
    21520 => '/wp-content/uploads/2026/09/combo-21520.png',
    21521 => '/wp-content/uploads/2026/09/combo-21521.png',
    21960 => '/wp-content/uploads/2026/09/combo-21960.png',
);
$sp_combos = array();
foreach ($sp_hero_ids as $spid) {
    $pp = get_post($spid);
    if ($pp && $pp->post_status === 'publish') $sp_combos[] = $pp;
}

function sp_combo_ahorro($pid) {
    $p = wc_get_product($pid);
    if (!$p) return 0;
    $sum = 0;
    foreach ($p->get_children() as $cid) {
        $c = wc_get_product($cid);
        if ($c) $sum += (float) $c->get_price();
    }
    $combo = (float) get_post_meta($pid, '_combo_price', true);
    $ahorro = $sum - $combo;
    return $ahorro > 0 ? $ahorro : 0;
}

// Categorías principales
$sp_cats = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 6));

// Productos en oferta (categoría "Descuento Online") — solo disponibles
$sp_onsale = array_values(array_filter(
    wc_get_products(array('limit' => 12, 'status' => 'publish', 'category' => array('descuento-online'))),
    'sp_disponible'
));
$sp_onsale = array_slice($sp_onsale, 0, 8);

// Más vendidos — solo disponibles
$sp_best = array_values(array_filter(
    wc_get_products(array('limit' => 12, 'status' => 'publish', 'orderby' => 'popularity', 'order' => 'DESC')),
    'sp_disponible'
));
$sp_best = array_slice($sp_best, 0, 4);

// Videos (Cloudinary) + enlace a la página de marca
$sp_videos = array(
    array('marca' => 'EVOGEN',            'url' => 'https://res.cloudinary.com/jrm5xhxt/video/upload/v1790785479/u0wc07yncqaiun9uqmat.mp4', 'link' => '/product-brand/suplementos-evogen/'),
    array('marca' => 'LANDERFIT',          'url' => 'https://res.cloudinary.com/jrm5xhxt/video/upload/v1790785485/tvobqmrpxt4jw29oodjt.mp4', 'link' => '/product-brand/suplementos-landerfit/'),
    array('marca' => 'Optimum Nutrition', 'url' => 'https://res.cloudinary.com/jrm5xhxt/video/upload/v1790785501/dh4t7edowegpotkphu9k.mp4', 'link' => '/product-brand/suplementos-optimum-nutrition/'),
    array('marca' => 'Dymatize',           'url' => 'https://res.cloudinary.com/jrm5xhxt/video/upload/v1790785504/devtid9okueywt8eqlls.mp4', 'link' => '/product-brand/suplementos-dymatize/'),
);

get_header();
?>
<style>
:root{
  --sp-primary: var(--primary, #E20613);
  --sp-accent: var(--accent, #151515);
  --sp-text: var(--text, #464646);
  --sp-text-light: var(--text_light, #888888);
  --sp-light: var(--light, #F4F4F4);
  --sp-border: var(--border, #E8E8E8);
  --sp-heading: var(--e-global-typography-accent-font-family, "Nutritix Heading", "Plus Jakarta Sans", Helvetica, Arial, sans-serif);
}
.sp-home{width:100%;overflow-x:hidden;color:var(--sp-text)}
.sp-home *{box-sizing:border-box}
.sp-home h1,.sp-home h2,.sp-home h3,.sp-home h4{font-family:var(--sp-heading);color:var(--sp-accent);margin:0 0 .5em}
.sp-wrap{max-width:none;margin:0 auto;padding:0 32px}
@media(max-width:600px){.sp-wrap{padding:0 16px}}
/* Liberar el contenedor del tema para que el home use el 100% del ancho */
body .site-content .col-full{max-width:none !important;padding-left:0 !important;padding-right:0 !important}
body .site-content .col-full .woocommerce{max-width:none !important}
body .site-content{padding-left:0 !important;padding-right:0 !important}
.sp-sec{padding:56px 0}
.sp-sec--tight{padding:40px 0}
.sp-eyebrow{font-family:var(--sp-heading);text-transform:uppercase;letter-spacing:.08em;font-size:12px;font-weight:800;color:var(--sp-primary)}
.sp-sec h2{font-size:32px;text-transform:uppercase;letter-spacing:-.01em}
.sp-sec p.sp-sub{color:var(--sp-text);margin-top:4px}
.sp-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font-family:var(--sp-heading);font-weight:700;font-size:14px;padding:14px 26px;border-radius:6px;text-decoration:none;transition:.2s;cursor:pointer}
.sp-btn--primary{background:var(--sp-primary);color:#fff}
.sp-btn--dark{background:var(--sp-accent);color:#fff}
.sp-btn--wa{background:#00832f;color:#fff}
.sp-btn:hover{transform:translateY(-2px)}

/* HERO */
.sp-hero{background:var(--sp-accent);color:#fff;position:relative;overflow:hidden;border-bottom:1px solid #2a2a2a}
.sp-hero__bg{position:absolute;inset:0;opacity:.08;background-image:radial-gradient(var(--sp-primary) 1px,transparent 1px);background-size:24px 24px}
.sp-hero__in{position:relative;z-index:2;display:grid;grid-template-columns:1fr 1.15fr;gap:48px;align-items:center;padding:64px 0}
@media(max-width:900px){.sp-hero__in{grid-template-columns:1fr;padding:44px 0}}
.sp-hero__badge{display:inline-flex;align-items:center;gap:8px;background:var(--sp-primary);color:#fff;font-family:var(--sp-heading);font-weight:800;font-size:11px;letter-spacing:.06em;text-transform:uppercase;padding:6px 12px;border-radius:4px;margin-bottom:16px}
.sp-hero h1{color:#fff;font-size:44px;line-height:1.05;text-transform:uppercase;letter-spacing:-.02em;max-width:620px}
@media(max-width:900px){.sp-hero h1{font-size:32px}}
.sp-hero h1 .red{color:var(--sp-primary)}
.sp-hero p.lead{color:#E4E2E2;font-size:17px;max-width:560px}
.sp-chips{display:flex;flex-wrap:wrap;gap:10px;margin:18px 0}
.sp-chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#d9d9d9;background:#0e0e0e;border:1px solid #333;padding:6px 12px;border-radius:6px}
.sp-hero__cta{display:flex;gap:14px;flex-wrap:wrap;margin-top:10px}

/* Hero slider de combos */
.sp-slider{position:relative;background:linear-gradient(180deg,#111,#000);border:1px solid #2a2a2a;border-radius:16px;padding:20px;box-shadow:0 20px 50px rgba(0,0,0,.4)}
.sp-slider__stage{position:relative;aspect-ratio:1698/926;border-radius:12px;overflow:hidden;background:#0a0a0a}
.sp-slide{position:absolute;inset:0;opacity:0;transition:opacity .7s ease, transform 1.4s ease;transform:scale(1.06);display:block}
.sp-slide.is-active{opacity:1;transform:scale(1)}
.sp-slide img{width:100%;height:100%;object-fit:contain;display:block}
.sp-slider__sticker{position:absolute;top:-10px;right:-10px;background:var(--sp-primary);color:#fff;font-family:var(--sp-heading);font-weight:800;padding:8px 14px;border-radius:10px;transform:rotate(2deg);box-shadow:0 6px 16px rgba(0,0,0,.35);border:1px solid #ff6a6a;transition:.3s;z-index:3}
.sp-slider__meta{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid #262626}
.sp-slider__meta .name{font-family:var(--sp-heading);font-weight:700;color:#fff;font-size:16px;line-height:1.2}
.sp-slider__meta .reg{color:#9a9a9a;text-decoration:line-through;font-size:13px}
.sp-slider__meta .now{color:var(--sp-primary);font-family:var(--sp-heading);font-weight:800;font-size:22px}
.sp-dots{display:flex;gap:8px;justify-content:center;margin-top:14px}
.sp-dot{width:26px;height:4px;border-radius:3px;background:#3a3a3a;border:0;padding:0;cursor:pointer}
.sp-dot.is-active{background:var(--sp-primary)}

/* TRUST BAR */
.sp-trust{background:#fff;border-bottom:1px solid var(--sp-border)}
.sp-trust__in{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;padding:24px 0}
@media(max-width:900px){.sp-trust__in{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.sp-trust__in{grid-template-columns:1fr;gap:16px}}
.sp-trust__it{display:flex;align-items:center;gap:14px}
.sp-trust__ic{width:48px;height:48px;border-radius:10px;background:var(--sp-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px}
.sp-trust h3{font-size:14px;text-transform:uppercase;margin:0}
.sp-trust span{font-size:13px;color:var(--sp-text-light)}

/* CATEGORÍAS */
.sp-cats{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:24px}
@media(max-width:900px){.sp-cats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.sp-cats{grid-template-columns:1fr}}
.sp-cat{background:#fff;border:1px solid var(--sp-border);border-radius:12px;padding:22px;transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:12px}
.sp-cat:hover{box-shadow:0 10px 28px rgba(0,0,0,.08);border-color:#ccc}
.sp-cat__ic{width:52px;height:52px;border-radius:10px;background:#fdeceb;display:flex;align-items:center;justify-content:center;font-size:26px}
.sp-cat h3{font-size:18px;margin:0}
.sp-cat p{font-size:13px;color:var(--sp-text-light);margin:0}
.sp-cat .go{color:var(--sp-primary);font-family:var(--sp-heading);font-weight:700;font-size:14px;margin-top:auto}

/* COMBOS GRID */
.sp-combos{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:24px}
@media(max-width:900px){.sp-combos{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.sp-combos{grid-template-columns:1fr}}
.sp-card{background:#fff;border:1px solid var(--sp-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;position:relative}
.sp-card__img{aspect-ratio:1/1;background:var(--sp-light);display:flex;align-items:center;justify-content:center;padding:16px}
.sp-card__img img{max-width:100%;max-height:100%;object-fit:contain}
.sp-card__sticker{position:absolute;top:12px;left:12px;background:var(--sp-primary);color:#fff;font-family:var(--sp-heading);font-weight:800;font-size:12px;text-transform:uppercase;padding:6px 10px;border-radius:6px}
.sp-card__body{padding:18px}
.sp-card__body .tag{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--sp-text-light);font-weight:700}
.sp-card__body h3{font-size:16px;margin:4px 0 10px}
.sp-card__price{display:flex;align-items:baseline;gap:10px;margin:10px 0}
.sp-card__price .reg{color:var(--sp-text-light);text-decoration:line-through;font-size:13px}
.sp-card__price .now{color:var(--sp-primary);font-family:var(--sp-heading);font-weight:800;font-size:22px}
.sp-card .sp-btn{width:100%}

/* BLACK WEEKEND BANNER */
.sp-bw{margin:0 auto}
.sp-bw__box{position:relative;display:block;border-radius:16px;overflow:hidden;text-decoration:none;background:var(--sp-accent);min-height:340px}
.sp-bw__box img{width:100%;height:100%;object-fit:cover;display:block;min-height:340px}
.sp-bw__ph{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:#fff;text-align:center;padding:30px}
.sp-bw__ph span{font-size:13px;color:#bbb}
@media(max-width:768px){.sp-bw__box,.sp-bw__box img{min-height:260px}}

/* VIDEOS MARCAS */
.sp-videos{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
@media(max-width:900px){.sp-videos{grid-template-columns:repeat(2,1fr)}}
.sp-vid{display:block;text-decoration:none}
.sp-vid__box{position:relative;border-radius:14px;overflow:hidden;background:#000;border:1px solid var(--sp-border)}
.sp-vid video{width:100%;aspect-ratio:9/16;object-fit:cover;display:block;background:#000}
.sp-vid .lbl{display:block;text-align:center;margin-top:10px;font-family:var(--sp-heading);font-weight:700;font-size:14px;color:var(--sp-accent)}
.sp-vid:hover .lbl{color:var(--sp-primary)}

/* TESTIMONIOS */
.sp-tests{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:24px}
@media(max-width:900px){.sp-tests{grid-template-columns:1fr}}
.sp-test{background:#fff;border:1px solid var(--sp-border);border-radius:12px;padding:22px}
.sp-test .stars{color:#f5a623;font-size:16px;margin-bottom:8px}
.sp-test p{font-style:italic;font-size:14px}
.sp-test .who{font-family:var(--sp-heading);font-weight:700;color:var(--sp-accent);font-size:14px}

/* CTA WHATSAPP */
.sp-cta{background:var(--sp-accent);border-radius:18px;padding:44px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:24px;flex-wrap:wrap}
.sp-cta h2{color:#fff;font-size:28px;text-transform:uppercase;margin:0}
.sp-cta p{color:#c9c9c9;margin:6px 0 0}
</style>

<main class="sp-home">

  <!-- 1. HERO (combos, slider automático) -->
  <section class="sp-hero" id="combos">
    <div class="sp-hero__bg"></div>
    <div class="sp-wrap">
      <div class="sp-hero__in">
        <div>
          <div class="sp-hero__badge">Exclusivo Online · Ahorro Garantizado</div>
          <h1>COMBOS EXCLUSIVOS WEB: <span class="red">MÁXIMO RENDIMIENTO</span> AL MEJOR PRECIO</h1>
          <p class="lead">Ahorra comprando en combo. Stacks diseñados para volumen, definición y fuerza. Ofertas válidas solo para compras en línea.</p>
          <div class="sp-chips">
            <span class="sp-chip">Envíos a todo Panamá</span>
            <span class="sp-chip">Retiro gratis en 6 sucursales</span>
            <span class="sp-chip">Pagos Yappy / Tarjeta</span>
          </div>
          <div class="sp-hero__cta">
            <a class="sp-btn sp-btn--primary" href="/promociones/combos/">Ver Combos</a>
            <a class="sp-btn sp-btn--dark" href="#ofertas">Ofertas Web</a>
          </div>
        </div>

        <div class="sp-slider" id="sp-hero-slider">
          <?php if ($sp_combos):
            $i = 0; foreach ($sp_combos as $c):
              $prod = wc_get_product($c->ID);
              $combo_price = (float) get_post_meta($c->ID, '_combo_price', true);
              $sum = 0; if ($prod) { foreach ($prod->get_children() as $cid) { $ch = wc_get_product($cid); if ($ch) $sum += (float) $ch->get_price(); } }
              $ahorro = sp_combo_ahorro($c->ID);
              $img = isset($sp_hero_img[$c->ID]) ? home_url($sp_hero_img[$c->ID]) : (get_the_post_thumbnail_url($c->ID, 'large') ?: wc_placeholder_img_src());
          ?>
            <a class="sp-slide <?php echo $i===0?'is-active':''; ?>" href="<?php echo esc_url(get_permalink($c->ID)); ?>" data-ahorro="<?php echo $ahorro>0 ? esc_attr('AHORRA $'.number_format($ahorro,2)) : ''; ?>" data-reg="<?php echo $sum>0 ? esc_attr('$'.number_format($sum,2)) : ''; ?>" data-now="<?php echo esc_attr('$'.number_format($combo_price,2)); ?>" data-name="<?php echo esc_attr($prod->get_name()); ?>">
              <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($prod->get_name()); ?>" loading="<?php echo $i===0?'eager':'lazy'; ?>">
            </a>
          <?php $i++; endforeach; else: ?>
            <div class="sp-slide is-active"><img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt=""></div>
          <?php endif; ?>

          <div class="sp-slider__sticker" id="sp-hero-sticker"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- 1b. META del combo activo (debajo del slider, en móvil se ve bien) -->
  <section class="sp-sec--tight" style="background:#0e0e0e;color:#fff">
    <div class="sp-wrap" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
      <div>
        <div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:#9a9a9a;font-family:var(--sp-heading);font-weight:700">Combo destacado</div>
        <div id="sp-hero-name" style="font-family:var(--sp-heading);font-weight:700;font-size:18px;color:#fff"></div>
      </div>
      <div style="text-align:right">
        <span id="sp-hero-reg" style="color:#9a9a9a;text-decoration:line-through;font-size:14px"></span>
        <span id="sp-hero-now" style="color:var(--sp-primary);font-family:var(--sp-heading);font-weight:800;font-size:24px;margin-left:10px"></span>
      </div>
    </div>
  </section>

  <!-- 2. BARRA DE CONFIANZA -->
  <section class="sp-trust">
    <div class="sp-wrap">
      <div class="sp-trust__in">
        <div class="sp-trust__it"><div class="sp-trust__ic">🚚</div><div><h3>Envío Gratis</h3><span>En compras desde $150 a todo Panamá</span></div></div>
        <div class="sp-trust__it"><div class="sp-trust__ic">🏬</div><div><h3>Retiro Gratis</h3><span>En nuestras 6 sucursales</span></div></div>
        <div class="sp-trust__it"><div class="sp-trust__ic">✅</div><div><h3>100% Originales</h3><span>Directo de fábrica, lote trazable</span></div></div>
        <div class="sp-trust__it"><div class="sp-trust__ic">💬</div><div><h3>Asesoría WhatsApp</h3><span>Expertos en suplementación</span></div></div>
      </div>
    </div>
  </section>

  <!-- 3. CATEGORÍAS -->
  <section class="sp-sec">
    <div class="sp-wrap">
      <span class="sp-eyebrow">Catálogo Oficial</span>
      <h2>Explora por Categoría</h2>
      <p class="sp-sub">Encuentra el suplemento exacto para tus objetivos.</p>
      <div class="sp-cats">
        <?php if ($sp_cats && !is_wp_error($sp_cats)):
          foreach ($sp_cats as $cat):
            $link = get_term_link($cat);
            $count = $cat->count; ?>
          <a class="sp-cat" href="<?php echo esc_url($link); ?>">
            <div class="sp-cat__ic">🏷️</div>
            <h3><?php echo esc_html($cat->name); ?></h3>
            <p><?php echo $count; ?> productos</p>
            <span class="go">Explorar →</span>
          </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <!-- 4. COMBOS DESTACADOS -->
  <section class="sp-sec" style="background:var(--sp-light);border-top:1px solid var(--sp-border);border-bottom:1px solid var(--sp-border)">
    <div class="sp-wrap">
      <span class="sp-eyebrow">🔒 Solo Ventas Online</span>
      <h2>Combos Especiales Web</h2>
      <p class="sp-sub">Máxima sinergia con descuento directo al carrito.</p>
      <div class="sp-combos">
        <?php if ($sp_combos):
          foreach (array_slice($sp_combos, 0, 3) as $c):
            $prod = wc_get_product($c->ID);
            $ahorro = sp_combo_ahorro($c->ID);
            $combo_price = (float) get_post_meta($c->ID, '_combo_price', true);
            $sum = 0; foreach ($prod->get_children() as $cid){ $ch=wc_get_product($cid); if($ch) $sum += (float)$ch->get_price(); }
            $pct = $sum>0 ? round(($ahorro/$sum)*100) : 0;
            $img = get_the_post_thumbnail_url($c->ID, 'medium') ?: wc_placeholder_img_src();
        ?>
          <div class="sp-card">
            <?php if ($ahorro>0): ?><div class="sp-card__sticker">Ahorra $<?php echo number_format($ahorro,2); ?></div><?php endif; ?>
            <div class="sp-card__img"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($prod->get_name()); ?>" loading="lazy"></div>
            <div class="sp-card__body">
              <span class="tag">Combo Web</span>
              <h3><?php echo esc_html($prod->get_name()); ?></h3>
              <div class="sp-card__price">
                <?php if ($sum>0): ?><span class="reg">$<?php echo number_format($sum,2); ?></span><?php endif; ?>
                <span class="now">$<?php echo number_format($combo_price,2); ?></span>
                <?php if ($pct>0): ?><span style="font-size:12px;font-weight:700;color:#00832f;background:#e6f6ea;border:1px solid #bfe6c8;padding:2px 6px;border-radius:4px"><?php echo $pct; ?>% OFF</span><?php endif; ?>
              </div>
              <a class="sp-btn sp-btn--primary" href="<?php echo esc_url(get_permalink($c->ID)); ?>">Ver combo</a>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <!-- 5. BLACK WEEKEND BANNER -->
  <section class="sp-sec--tight">
    <div class="sp-wrap">
      <a class="sp-bw__box" href="/black-weekend/">
        <!-- Reemplazar por imagen real: Desktop 1920x500 / Móvil 1080x720 -->
        <div class="sp-bw__ph">
          <div class="sp-hero__badge" style="margin:0">Solo 3 días</div>
          <h2 style="color:#fff;font-size:34px;text-transform:uppercase;margin:6px 0">BLACK WEEKEND</h2>
          <span>Descuentos exclusivos comprando online — banner pendiente de imagen</span>
        </div>
      </a>
    </div>
  </section>

  <!-- 6. OFERTAS WEB-ONLY -->
  <section class="sp-sec" id="ofertas">
    <div class="sp-wrap">
      <span class="sp-eyebrow">Precio Web</span>
      <h2>Ofertas Exclusivas Online</h2>
      <p class="sp-sub">Descuentos directos por comprar en la web.</p>
      <div class="sp-combos" style="grid-template-columns:repeat(4,1fr)">
        <?php if ($sp_onsale):
          foreach ($sp_onsale as $p):
            $img = get_the_post_thumbnail_url($p->get_id(), 'medium') ?: wc_placeholder_img_src();
            $reg = (float) $p->get_regular_price();
            $sale = (float) $p->get_price();
            $sp_pct = function_exists('sp_pct_ahorro') ? sp_pct_ahorro($p) : 0;
        ?>
          <div class="sp-card">
            <div class="sp-card__sticker">Ahorras <?php echo $sp_pct; ?>%</div>
            <div class="sp-card__img"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy"></div>
            <div class="sp-card__body">
              <h3 style="font-size:14px"><?php echo esc_html($p->get_name()); ?></h3>
              <div class="sp-card__price">
                <?php if ($reg>$sale): ?><span class="reg">$<?php echo number_format($reg,2); ?></span><?php endif; ?>
                <span class="now">$<?php echo number_format($sale,2); ?></span>
              </div>
              <?php if ($sp_pct>0): ?><div style="font-size:12px;font-weight:700;color:#00832f;margin:2px 0 8px">Compra online y ahorras <?php echo $sp_pct; ?>%</div><?php endif; ?>
              <a class="sp-btn sp-btn--dark" href="<?php echo esc_url($p->get_permalink()); ?>">Ver / Añadir</a>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p class="sp-sub">(Sección reservada: aquí irán los productos con descuento web-only.)</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- 7. MÁS VENDIDOS -->
  <section class="sp-sec" style="background:var(--sp-light);border-top:1px solid var(--sp-border)">
    <div class="sp-wrap">
      <span class="sp-eyebrow">Los Favoritos de Panamá</span>
      <h2>Más Vendidos</h2>
      <div class="sp-combos" style="grid-template-columns:repeat(4,1fr)">
        <?php if ($sp_best): foreach ($sp_best as $p):
            $img = get_the_post_thumbnail_url($p->get_id(), 'medium') ?: wc_placeholder_img_src(); ?>
          <div class="sp-card">
            <div class="sp-card__img"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy"></div>
            <div class="sp-card__body">
              <h3 style="font-size:14px"><?php echo esc_html($p->get_name()); ?></h3>
              <div class="sp-card__price"><span class="now">$<?php echo number_format((float)$p->get_price(),2); ?></span></div>
              <a class="sp-btn sp-btn--primary" href="<?php echo esc_url($p->get_permalink()); ?>">Añadir al carrito</a>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <!-- 8. VIDEOS DE MARCA -->
  <section class="sp-sec">
    <div class="sp-wrap">
      <span class="sp-eyebrow">Marcas Oficiales</span>
      <h2>Las marcas que nos eligen</h2>
      <p class="sp-sub">Productos originales de las mejores marcas del mundo.</p>
      <div class="sp-videos" id="sp-videos">
        <?php foreach ($sp_videos as $v): ?>
          <a class="sp-vid" href="<?php echo esc_url($v['link']); ?>">
            <div class="sp-vid__box">
              <video muted loop playsinline preload="none" data-src="<?php echo esc_url($v['url']); ?>" aria-label="<?php echo esc_attr($v['marca']); ?>"></video>
            </div>
            <span class="lbl"><?php echo esc_html($v['marca']); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 9. TESTIMONIOS -->
  <section class="sp-sec" style="background:var(--sp-light);border-top:1px solid var(--sp-border)">
    <div class="sp-wrap">
      <span class="sp-eyebrow">Comunidad & Confianza</span>
      <h2>Lo que dicen nuestros clientes</h2>
      <div class="sp-tests">
        <div class="sp-test"><div class="stars">★★★★★</div><p>“Pedí el combo y en la tarde ya lo tenía. Productos originales y excelente asesoría.”</p><div class="who">Cliente — Ciudad de Panamá</div></div>
        <div class="sp-test"><div class="stars">★★★★★</div><p>“Los precios web de los combos son los mejores. Retiro gratis en sucursal.”</p><div class="who">Cliente — San Francisco</div></div>
        <div class="sp-test"><div class="stars">★★★★★</div><p>“La asesoría por WhatsApp me ayudó a armar mi stack según mi objetivo.”</p><div class="who">Cliente — Panamá</div></div>
      </div>
    </div>
  </section>

  <!-- 10. CTA WHATSAPP -->
  <section class="sp-sec--tight">
    <div class="sp-wrap">
      <div class="sp-cta">
        <div>
          <h2>¿No sabes cuál elegir?</h2>
          <p>Chatea con nuestros asesores y recibe un plan personalizado en minutos.</p>
        </div>
        <a class="sp-btn sp-btn--wa" href="https://wa.me/50760153257?text=Hola%20Suplementos%20Panam%C3%A1,%20deseo%20asesor%C3%ADa" target="_blank" rel="noopener">Asesoría por WhatsApp: +507 6015-3257</a>
      </div>
    </div>
  </section>

</main>

<script>
(function(){
  /* --- Hero slider de combos --- */
  var root = document.getElementById('sp-hero-slider');
  if (root) {
    var slides = root.querySelectorAll('.sp-slide');
    var dotsWrap = document.createElement('div'); dotsWrap.className='sp-dots';
    var sticker = document.getElementById('sp-hero-sticker');
    var elName = document.getElementById('sp-hero-name');
    var elReg = document.getElementById('sp-hero-reg');
    var elNow = document.getElementById('sp-hero-now');
    var idx = 0, timer = null, DUR = 5000;

    function render(){
      slides.forEach(function(s,i){ s.classList.toggle('is-active', i===idx); });
      var s = slides[idx];
      if (!s) return;
      if (sticker) sticker.textContent = s.dataset.ahorro || '';
      if (sticker) sticker.style.display = s.dataset.ahorro ? '' : 'none';
      if (elName) elName.textContent = s.dataset.name || '';
      if (elReg) elReg.textContent = s.dataset.reg || '';
      if (elNow) elNow.textContent = s.dataset.now || '';
      dotsWrap.querySelectorAll('.sp-dot').forEach(function(d,i){ d.classList.toggle('is-active', i===idx); });
    }
    slides.forEach(function(_,i){
      var b=document.createElement('button'); b.className='sp-dot'+(i===0?' is-active':''); b.type='button'; b.setAttribute('aria-label','Combo '+(i+1));
      b.addEventListener('click', function(){ idx=i; render(); restart(); });
      dotsWrap.appendChild(b);
    });
    root.appendChild(dotsWrap);
    function next(){ idx=(idx+1)%slides.length; render(); }
    function start(){ if(slides.length>1) timer=setInterval(next, DUR); }
    function stop(){ clearInterval(timer); }
    function restart(){ stop(); start(); }
    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    document.addEventListener('visibilitychange', function(){ document.hidden?stop():start(); });

    /* IntersectionObserver: arranca solo cuando es visible */
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function(es){
        es.forEach(function(e){ e.isIntersecting ? start() : stop(); });
      }, {threshold:.3});
      io.observe(root);
    } else { start(); }
    render();
  }

  /* --- Videos de marca: carga y reproduce al entrar en viewport --- */
  var vids = document.querySelectorAll('#sp-videos video');
  function loadVid(v){ if(v.dataset.loaded) return; v.dataset.loaded=1; var s=document.createElement('source'); s.src=v.dataset.src; s.type='video/mp4'; v.appendChild(s); v.load(); }
  var currentPlaying = [];
  if ('IntersectionObserver' in window) {
    var vIO = new IntersectionObserver(function(es){
      es.forEach(function(e){
        var v=e.target;
        if (e.isIntersecting && e.intersectionRatio>=.5) {
          currentPlaying.forEach(function(o){ if(o!==v) o.pause(); });
          loadVid(v); var pr=v.play(); if(pr) pr.catch(function(){});
          currentPlaying=[v];
        } else { v.pause(); }
      });
    }, {threshold:[0,.5,1], rootMargin:'120px'});
    vids.forEach(function(v){ vIO.observe(v); });
  }
  document.addEventListener('visibilitychange', function(){ if(document.hidden) vids.forEach(function(v){v.pause();}); });
})();
</script>

<?php get_footer(); ?>
