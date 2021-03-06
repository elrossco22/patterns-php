<?php

namespace eLife\Patterns\ViewModel;

use eLife\Patterns\ArrayAccessFromProperties;
use eLife\Patterns\ArrayFromProperties;
use eLife\Patterns\ComposedAssets;
use eLife\Patterns\ViewModel;
use InvalidArgumentException;
use Traversable;

final class CaptionedAsset implements ViewModel
{
    use ArrayAccessFromProperties;
    use ArrayFromProperties;
    use ComposedAssets;

    private $captionText;
    private $picture;
    private $video;
    private $table;
    private $image;
    private $doi;
    private $inline;

    public function __construct(
        IsCaptioned $figure,
        CaptionText $captionText = null,
        Doi $doi = null,
        bool $inline = false
    ) {
        $this->captionText = $captionText;
        $this->setFigure($figure);
        if ($doi !== null) {
            $doi = FlexibleViewModel::fromViewModel($doi);
            $this->doi = $doi->withProperty('variant', Doi::ASSET);
        }
        if ($inline) {
            $this->inline = $inline;
        }
    }

    private function setFigure($figure)
    {
        // Reverse switch (i.e. which evaluates to true)
        switch (true) {
            case $figure instanceof Image:
                $this->image = $figure;
                break;

            case $figure instanceof Picture:
                $this->picture = $figure;
                break;

            case $figure instanceof Video:
                $this->video = $figure;
                break;

            case $figure instanceof Table:
                $this->table = $figure;
                break;

            default:
                throw new InvalidArgumentException('Unknown figure type '.get_class($figure));
        }
    }

    public function getLocalStyleSheets() : Traversable
    {
        yield 'resources/assets/css/captioned-asset.css';
    }

    public function getTemplateName() : string
    {
        return 'resources/templates/captioned-asset.mustache';
    }

    protected function getComposedViewModels() : Traversable
    {
        yield $this->captionText;
        yield $this->picture;
        yield $this->doi;
        yield $this->video;
        yield $this->table;
    }
}
