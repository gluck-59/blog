{include file='header.tpl'}
    <h1 class="mb-4">{$title|default:'Блог'}</h1>

{*{$categories|print_r}*}

    {if $categories|@count > 0}
        {foreach $categories as $category}
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h4 mb-0">{$category.name} <small class="text-muted">{$category.description}</small></h2>
                    <a class="btn btn-sm btn-link" href="?route=category&id={$category.id}">Все статьи<span class="d-none d-lg-inline"> {$category.name}</span> →</a>
                </div>
{*                {if $category.description}*}
{*                    <p class="text-muted">{$category.description}</p>*}
{*                {/if}*}

                {if $category.posts|@count > 0}
                    <div class="row g-3">
                        {foreach $category.posts as $post}
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm post-card">
                                    {if $post.image}
                                        <img src="/src/{$smarty.const.IMG_DIR}{$post.image}" alt="{$post.title}" class="post-card-img">
                                    {/if}
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h3 class="h5 card-title">
                                            <a href="?route=post&id={$post.id}" class="stretched-link text-decoration-none" title="{$post.title}">{$post.title}</a>
                                        </h3>
                                        <div>
                                            {if $post.short_description}
                                                <p class="card-text flex-grow-1">{$post.short_description}</p>
                                            {/if}
                                            <p class="text-muted small mb-2 d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ccc" class="bi bi-calendar" viewBox="0 0 16 16">
                                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                                </svg>
                                                &nbsp;{$post.published_at|date_format:"d.m.Y H:i"}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {else}
                    <p class="text-muted">В этой категории пока нет статей.</p>
                {/if}
            </section>
        {/foreach}
    {else}
        <div class="alert alert-info">
            <p>Контента пока нет. Запустите сидинг: <a id="seedUrl" href=""></a></p>
            <p>Внешние API могут подтормаживать.</p>
        </div>
    {/if}
{include file='footer.tpl'}
