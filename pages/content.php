<?php if ($page==='home'): ?>
    <?php foreach($filtered as $key=>$post): ?>
      <article class="post-card">
        <img src="<?= htmlspecialchars($post['image']) ?>" alt="">
        <span class="badge"><?=htmlspecialchars($post['category'])?></span>
        <h4><?=htmlspecialchars($post['title'])?></h4>
        <div class="date"><?=date('j F, Y', strtotime($post['date'] ?? 'now'))?></div>
        <p class="excerpt"><?=htmlspecialchars($post['summary'])?></p>
        <a 
          class="read-more" 
          href="<?=htmlspecialchars($post['link'])?>" 
          target="_blank" 
          rel="noopener"
        >
        Đọc thêm
        </a>
      </article>
    <?php endforeach; ?>
<?php endif; ?>






