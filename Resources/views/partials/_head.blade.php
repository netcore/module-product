{{ Breadcrumbs::render($breadcrumb ?? 'admin', $breadcrumbData ?? null) }}

<div class="page-header">
    <h1>
        <span class="text-muted font-weight-light">
            <i class="page-header-icon {{ $icon ?? 'fa fa-pencil' }}"></i>
            {{ $title ?? 'Resource management' }}
        </span>
    </h1>
</div>