<?php $this->layout("_theme", ['head' => $head]); ?>

<section class="blog_page">
  <header class="blog_page_header">
    <h1><?= ($title ?? "BLOG"); ?></h1>
    <p><?= ($search ?? "Confira nossas dicas para controlar melhor suas contas"); ?></p>
    <form name="search" action="<?= url("/blog/buscar"); ?>" method="post" enctype="multipart/form-data">
      <label>
        <input type="text" name="s" placeholder="Encontre um artigo:"/>
        <button class="icon-search icon-notext"></button>
      </label>
    </form>
  </header>

  <?php if (empty($blog) && !empty($search)): ?>
    <div class="content content">
      <div class="empty_content">
        <h3 class="empty_content_title">Sua pesquisa não retornou resultados :/</h3>
        <p class="empty_content_desc">Você pesquuisou por <b><?= $search; ?></b>. Tente outros termos.</p>
        <a href="<?= url("/blog"); ?>" title="Blog"
           class="empty_content_btn gradient gradient-green gradient-hover radius">
          ... ou volte ao blog
        </a>
      </div>
    </div>
  <?php elseif (empty($blog)): ?>
    <div class="content content">
      <div class="empty_content">
        <h3 class="empty_content_title">Ainda estamos trabalhando aqui!</h3>
        <p class="empty_content_desc">Nossos editores estão preparando um conteúdo de primeira pra você.</p>
      </div>
    </div>
  <?php else: ?>
    <div class="blog_content container content">
      <div class="blog_articles">
        <?php for ($i = 0; $i <= 8; $i++): ?>
          <?php $this->insert("blog-list"); ?>
        <?php endfor; ?>
      </div>
      <?= $paginator; ?>
    </div>
  <?php endif; ?>
</section>