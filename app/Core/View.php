<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Rendert een view binnen het gedeelde layout (header/sidebar/footer).
 * Presentatielaag heeft geen kennis van BLL/DAL-implementaties; controllers
 * geven kant-en-klare data mee.
 */
final class View
{
    private const VIEWS_PAD = __DIR__ . '/../Presentation/Views/';

    public static function render(string $view, array $data = []): void
    {
        $data['flash'] = $data['flash'] ?? Flash::ophalenEnWissen();
        extract($data, EXTR_SKIP);

        $viewBestand = self::VIEWS_PAD . $view . '.php';
        if (!is_file($viewBestand)) {
            throw new \RuntimeException("View niet gevonden: {$view}");
        }

        require self::VIEWS_PAD . 'layout/header.php';
        require self::VIEWS_PAD . 'layout/sidebar.php';
        echo '<main class="content py-4">';
        require self::VIEWS_PAD . 'layout/flash.php';
        require $viewBestand;
        echo '</main>';
        require self::VIEWS_PAD . 'layout/footer.php';
    }

    public static function renderZonderLayout(string $view, array $data = []): void
    {
        $data['flash'] = $data['flash'] ?? Flash::ophalenEnWissen();
        extract($data, EXTR_SKIP);
        $viewBestand = self::VIEWS_PAD . $view . '.php';
        if (!is_file($viewBestand)) {
            throw new \RuntimeException("View niet gevonden: {$view}");
        }
        require $viewBestand;
    }
}
