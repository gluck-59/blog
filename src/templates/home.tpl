{include file='header.tpl'}
    <h1 class="mb-4">{$title|default:'Блог'}</h1>
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
                                        <img src="{$smarty.const.IMG_DIR}{$post.image}" alt="{$post.title}" class="post-card-img">
                                    {/if}
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h3 class="h5 card-title">
                                            <a href="?route=post&id={$post.id}" class="stretched-link text-decoration-none" title="{$post.title}">{$post.title}</a>
                                        </h3>
                                        <div>
                                            {if $post.short_description}
                                                <p class="card-text flex-grow-1">{$post.short_description}</p>
                                            {/if}
                                            <p class="card-text text-muted small mb-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ccc" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                </svg>
                                                {$post.published_at|rus_date}
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
