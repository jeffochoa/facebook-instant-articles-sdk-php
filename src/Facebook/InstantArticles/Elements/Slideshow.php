<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Class SlideShow
 * This element Class is the slideshow for the article.
 *
 * Example:
 * <figure class="op-slideshow">
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img1.jpg" />
 *     </figure>
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img2.jpg" />
 *     </figure>
 *     <figure>
 *         <img src="http://mydomain.com/path/to/img3.jpg" />
 *     </figure>
 * </figure>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/image}
 */
class Slideshow extends Audible
{
    /**
     * @var Caption The caption for the Slideshow
     */
    private $caption;

    /**
     * @var Image[] the images hosted on web that will be shown on the slideshow
     */
    private $article_images = array();

    /**
     * @var string The json geotag content inside the script geotag
     */
    private $geotag;

    /**
     * @var Audio The audio if the Slideshow uses audio
     */
    private $audio;

    /**
     * @var string The attribution citation text in the <cite>...</cite> tags.
     */
    private $attribution;

    /**
     * Private constructor.
     * @see Slideshow::create();.
     */
    private function __construct()
    {
    }

    /**
     * Factory method for the Slideshow
     * @return Slideshow the new instance
     */
    public static function create()
    {
        return new self();
    }

    /**
     * This sets figcaption tag as documentation. It overrides all sets
     * made with @see Caption.
     *
     * @param Caption the caption the slideshow will have
     */
    public function withCaption($caption)
    {
        Type::enforce($caption, Caption::class);
        $this->caption = $caption;

        return $this;
    }

    /**
     * Sets the Image list of images for the slideshow. It is REQUIRED.
     *
     * @param Image[] The images. Ie: http://domain.com/img.png
     */
    public function withImages($article_images)
    {
        Type::enforceArrayOf($article_images, Image::class);
        $this->article_images = $article_images;

        return $this;
    }

    /**
     * Adds a new image to the slideshow. It is REQUIRED.
     *
     * @param string[] The url of image. Ie: http://domain.com/img.png
     */
    public function addImage($article_image)
    {
        Type::enforce($article_image, Image::class);
        $this->article_images[] = $article_image;

        return $this;
    }

    /**
     * Sets the geotag on the slideshow.
     *
     * @see {link:http://geojson.org/}
     */
    public function withMapGeoTag($json)
    {
        Type::enforce($json, Type::STRING);
        $this->geotag = $json; // TODO Validate the json informed

        return $this;
    }

    /**
     * Adds audio to this slideshow.
     *
     * @param Audio The audio object
     */
    public function withAudio($audio)
    {
        Type::enforce($audio, Audio::class);
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return Caption The caption object
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @return Image[] The ArticleImages content of the slideshow
     */
    public function getArticleImages()
    {
        return $this->article_images;
    }

    /**
     * @return string the json for geotag unescaped content
     */
    public function getGeotag()
    {
        return $this->geotag;
    }

    /**
     * @return Audio The audio object
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * @return string the <cite> content
     */
    public function getAttribution()
    {
        return $this->attribution;
    }

    /**
     * Structure and create the full Slideshow in a XML format DOMElement.
     *
     * @param $document DOMDocument where this element will be appended. Optional
     */
    public function toDOMElement($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }
        $element = $document->createElement('figure');
        $element->setAttribute('class', 'op-slideshow');

        // URL markup required
        if ($this->article_images) {
            foreach ($this->article_images as $article_image) {
                $article_image_element = $article_image->toDOMElement($document);
                $element->appendChild($article_image_element);
            }
        }

        // Caption markup optional
        if ($this->caption) {
            $element->appendChild($this->caption->toDOMElement($document));
        }

        // Geotag markup optional
        if ($this->geotag) {
            $script_element = $document->createElement('script');
            $script_element->setAttribute('type', 'application/json');
            $script_element->setAttribute('class', 'op-geotag');
            $script_element->appendChild($document->createTextNode($this->geotag));
            $element->appendChild($script_element);
        }

        // Audio markup optional
        if ($this->audio) {
            $element->appendChild($this->audio->toDOMElement($document));
        }

        return $element;
    }
}