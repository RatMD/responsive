Responsive - OctoberCMS Plugin
==============================

**Responsive** allows you to provide different responsive sizes and formats of your images.

Features
--------

-   Provide different formats and dimensions of your images.


Requirements
------------

- PHP 7.4+ || 8.0+
- OctoberCMS v2+ || v3+
- GD || GMagick (GraphicsMagick) || IMagick (ImageMagick)


Template Markups
----------------

**Responsive** adds the following template markups.

### convert

`convert` is registered as TWIG filter and can be used to provide one single additional format or 
size of the desired image. The desired values are passed inside an object, you need to pass at least 
one conversion argument (either a size or a format ... or, of course, both).

#### Syntax


```js
convert({ width: 1280 })
convert({ width: 1280, height: 720 })
convert({ width: 1280, height: 720, format: 'webp' })
```

Supported Parameters:

Example:

```html
<picture>
    <source srcset="{{ 'assets/imgs/image.jpeg'|theme|convert(width=1024, format='image/webp') }}" media="(min-width: 768px)" type="image/webp" />
    <source srcset="{{ 'assets/imgs/image.jpeg'|theme|convert(width=1024) }}" media="(min-width: 768px)" />
    <source srcset="{{ 'assets/imgs/image.jpeg'|theme|convert(width=768, format='image/webp') }}" type="image/webp" />
    <source srcset="{{ 'assets/imgs/image.jpeg'|theme|convert(width=768) }}" />
    <img src="{{ 'assets/imgs/image.jpg'|theme }}" alt="Image" />
</picture>
```

### picture

`picture` is registered as TWIG function and generates a `<picture>` element with the desired 
source set.

```html
{{ 
    picture(
        file: 'assets/imgs/image.jpg'|theme,
        formats: ['webp', 'jpg'],
        breakpoints: {
            0: [768],
            768: [1024],
            1024: [1200],
            1200: [1920]
        },
        attributes: {
            loading: 'lazy',
            'data-custom': '12'
        }
    )
}}
```
