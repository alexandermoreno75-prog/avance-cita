<?php

if ($totalPages <= 1) {
    return;
}

$buildUrl = static function (
    int $targetPage
) use (
    $term,
    $perPage,
    $baseUrl
): string {
    $params = [
        'page' => $targetPage
    ];

    if ($term !== '') {
        $params['q'] = $term;
    }

    if ($perPage !== 10) {
        $params['per_page'] = $perPage;
    }

    return url(
        $baseUrl . '?' . http_build_query($params)
    );
};

?>

<nav
    class="pagination"
    aria-label="Paginación de resultados"
>

    <p class="pagination-summary">

        Mostrando

        <strong>
            <?= e(
                (string) (
                    ($page - 1) * $perPage + 1
                )
            ) ?>
        </strong>

        a

        <strong>
            <?= e(
                (string) min(
                    $page * $perPage,
                    $total
                )
            ) ?>
        </strong>

        de

        <strong>
            <?= e((string) $total) ?>
        </strong>

        pacientes.

    </p>


    <ul class="pagination-list">

        <?php if ($page > 1): ?>

            <li>

                <a
                    class="button secondary"
                    href="<?= e(
                        $buildUrl($page - 1)
                    ) ?>"
                    rel="prev"
                    aria-label="Página anterior"
                >
                    ← Anterior
                </a>

            </li>

        <?php endif; ?>


        <?php

        $window = 2;

        $start = max(
            1,
            $page - $window
        );

        $end = min(
            $totalPages,
            $page + $window
        );

        ?>


        <?php if ($start > 1): ?>

            <li>

                <a
                    class="button secondary"
                    href="<?= e($buildUrl(1)) ?>"
                >
                    1
                </a>

            </li>

            <?php if ($start > 2): ?>

                <li class="paginationdots">
                    …
                </li>

            <?php endif; ?>

        <?php endif; ?>


        <?php for (
            $i = $start;
            $i <= $end;
            $i++
        ): ?>

            <li>

                <?php if ($i === $page): ?>

                    <span
                        class="button primary"
                        aria-current="page"
                    >
                        <?= e((string) $i) ?>
                    </span>

                <?php else: ?>

                    <a
                        class="button secondary"
                        href="<?= e($buildUrl($i)) ?>"
                    >
                        <?= e((string) $i) ?>
                    </a>

                <?php endif; ?>

            </li>

        <?php endfor; ?>


        <?php if ($end < $totalPages): ?>

            <?php if (
                $end < $totalPages - 1
            ): ?>

                <li class="paginationdots">
                    …
                </li>

            <?php endif; ?>


            <li>

                <a
                    class="button secondary"
                    href="<?= e(
                        $buildUrl($totalPages)
                    ) ?>"
                >
                    <?= e((string) $totalPages) ?>
                </a>

            </li>

        <?php endif; ?>


        <?php if ($page < $totalPages): ?>

            <li>

                <a
                    class="button secondary"
                    href="<?= e(
                        $buildUrl($page + 1)
                    ) ?>"
                    rel="next"
                    aria-label="Página siguiente"
                >
                    Siguiente →
                </a>

            </li>

        <?php endif; ?>

    </ul>

</nav>