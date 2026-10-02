<?php
/**
 * Template Name: Black Weekend (Listado de Descuentos)
 * Description: Lista todos los productos con descuento (oferta) para Black Weekend.
 */
if (!defined('ABSPATH')) exit;

$sp_products = wc_get_products(array(
    'limit'   => -1,
    'status'  => 'publish',
    'on_sale' => true,
    'orderby' => 'date',
    'order'   => 'DESC',
));

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
.sp-home{width:100%;color:var(--sp-text)}
body .site-content .col-full{max-width:none !important;padding-left:0 !important;padding-right:0 !important}
body .content-area,body .site-content .content-area{max-width:none !important;width:100% !important;float:none !important}
body .site-content{margin-top:0 !important;margin-bottom:0 !important;padding-left:0 !important;padding-right:0 !important}
body .breadcrumb-wrap{display:none !important}
.sp-home *{box-sizing:border-box;border-radius:0 !important}
.sp-home h1,.sp-home h2,.sp-home h3{font-family:var(--sp-heading);color:var(--sp-accent);margin:0 0 .5em}
.sp-wrap{max-width:1280px;margin:0 auto;padding:0 16px}
.sp-sec{padding:48px 0}
.sp-eyebrow{font-family:var(--sp-heading);text-transform:uppercase;letter-spacing:.08em;font-size:12px;font-weight:800;color:var(--sp-primary)}
.sp-sec h2{font-size:34px;text-transform:uppercase;letter-spacing:-.01em}
.sp-sub{color:var(--sp-text);margin-top:4px}
.sp-bw-head{background:var(--sp-accent);color:#fff;border-radius:16px;padding:38px;margin:24px 0 8px}
.sp-bw-head h1{color:#fff;font-size:40px;text-transform:uppercase;margin:0 0 6px}
.sp-bw-head p{color:#c9c9c9;margin:0}
.sp-bw-head .badge{display:inline-block;background:var(--sp-primary);color:#fff;font-family:var(--sp-heading);font-weight:800;font-size:12px;text-transform:uppercase;padding:6px 12px;border-radius:4px;margin-bottom:12px}
.sp-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:24px}
@media(max-width:1024px){.sp-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){.sp-grid{grid-template-columns:repeat(2,1fr)}}
.sp-card{background:#fff;border:1px solid var(--sp-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;position:relative;text-decoration:none}
.sp-card__sticker{position:absolute;top:12px;left:12px;background:var(--sp-primary);color:#fff;font-family:var(--sp-heading);font-weight:800;font-size:12px;text-transform:uppercase;padding:6px 10px;border-radius:6px;z-index:2}
.sp-card__img{aspect-ratio:1/1;background:var(--sp-light);display:flex;align-items:center;justify-content:center;padding:16px}
.sp-card__img img{max-width:100%;max-height:100%;object-fit:contain}
.sp-card__body{padding:16px}
.sp-card__body h3{font-size:14px;margin:0 0 8px;line-height:1.3}
.sp-card__price{display:flex;align-items:baseline;gap:10px}
.sp-card__price .reg{color:var(--sp-text-light);text-decoration:line-through;font-size:13px}
.sp-card__price .now{color:var(--sp-primary);font-family:var(--sp-heading);font-weight:800;font-size:22px}
.sp-empty{padding:40px;text-align:center;color:var(--sp-text-light)}
</style>

<main class="sp-home">
  <section class="sp-sec">
    <div class="sp-wrap">
      <div class="sp-bw-head">
        <span class="badge">Solo 3 días</span>
        <h1>Black Weekend</h1>
        <p>Descuentos exclusivos comprando online. Aprovecha antes de que terminen.</p>
      </div>

      <?php if ($sp_products): ?>
        <div class="sp-grid">
          <?php foreach ($sp_products as $p):
            $img = get_the_post_thumbnail_url($p->get_id(), 'medium') ?: wc_placeholder_img_src();
            if ($p->get_type() === 'variable') {
                $vp = $p->get_variation_prices();
                $reg = !empty($vp['regular_price']) ? (float) min($vp['regular_price']) : 0;
                $sale = !empty($vp['price']) ? (float) min($vp['price']) : 0;
            } else {
                $reg = (float) $p->get_regular_price();
                $sale = (float) $p->get_price();
            }
            $pct = ($reg > 0 && $sale < $reg) ? round((($reg - $sale) / $reg) * 100) : 0;
          ?>
            <a class="sp-card" href="<?php echo esc_url($p->get_permalink()); ?>">
              <?php if ($pct > 0): ?><span class="sp-card__sticker"><?php echo $pct; ?>% OFF</span><?php endif; ?>
              <div class="sp-card__img"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy"></div>
              <div class="sp-card__body">
                <h3><?php echo esc_html($p->get_name()); ?></h3>
                <div class="sp-card__price">
                  <?php if ($reg > $sale): ?><span class="reg">$<?php echo number_format($reg, 2); ?></span><?php endif; ?>
                  <span class="now">$<?php echo number_format($sale, 2); ?></span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="sp-empty">Aún no hay productos en oferta. (Se mostrarán aquí automáticamente.)</div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
