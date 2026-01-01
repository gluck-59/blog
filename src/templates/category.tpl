{include file='header.tpl'}
    <header class="mb-3">
        <h2 class="mb-1">{$category.name}</h2>
        {if $category.description}
            <p class="text-muted mb-0">{$category.description}</p>
        {/if}
    </header>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="me-2 text-muted">Сортировка:</span>
            <div class="btn-group btn-group-sm" role="group">
                <a href="?route=category&id={$category.id}&sort=date" class="btn btn-outline-primary{if $sort == 'date'} active{/if}">по дате</a>
                <a href="?route=category&id={$category.id}&sort=views" class="btn btn-outline-primary{if $sort == 'views'} active{/if}">по просмотрам</a>
            </div>
        </div>
    </div>

    {if $posts|@count > 0}
        <div class="row g-3 mb-3">
            {foreach $posts as $post}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm post-card">
                        {if $post.image}
                            <img src="{$smarty.const.IMG_DIR}{$post.image}" alt="{$post.title}" class="post-card-img">
                        {/if}
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h2 class="h5 card-title">
                                <a href="?route=post&id={$post.id}" class="stretched-link text-decoration-none" title="{$post.title}">{$post.title|mb_truncate:55}</a>
                            </h2>
                            <div>
                                {if $post.short_description}
                                    <p class="card-text flex-grow-1">{$post.short_description}</p>
                                {/if}
                                <p class="card-text text-muted small mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ccc" class="bi bi-calendar" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                    </svg>
                                    {$post.published_at|rus_date}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ccc" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                    </svg>
                                    {$post.views|escape}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>

        <nav aria-label="Пагинация">
            <ul class="pagination">
                {if $currentPage > 1}
                    <li class="page-item">
                        <a class="page-link" href="?route=category&id={$category.id}&sort={$sort}&page={$currentPage-1}">← Предыдущая</a>
                    </li>
                {/if}

                <li class="page-item disabled">
                    <span class="page-link">Страница {$currentPage} из {$totalPages}</span>
                </li>

                {if $currentPage < $totalPages}
                    <li class="page-item">
                        <a class="page-link" href="?route=category&id={$category.id}&sort={$sort}&page={$currentPage+1}">Следующая →</a>
                    </li>
                {/if}
            </ul>
        </nav>
    {else}
        <p class="text-muted">В этой категории пока нет статей.</p>
    {/if}

    <p class="mt-3"><a href="/" class="btn btn-link">← На главную</a></p>
{include file='footer.tpl'}
