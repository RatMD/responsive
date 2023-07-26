Responsive v0.1.0 / Documentation
=================================

Twig Markup
-----------

### convert
`convert` is registered as TWIG filter and can be used to provide one single additional format or 
size of the desired image. The desired values are passed inside an object, you need to pass at least 
one conversion argument (either a size or a format ... or, of course, both).

#### Syntax

The syntax requires an object as first argument, on which you can pass the desired conversion 
options. You need to pass at least one conversion argument, either the size (expressed with width 
and/or height) or the image format (ex. 'webp').

```js
convert({ width: 1280 })
convert({ width: 1280, height: 720 })
convert({ width: 1280, height: 720, format: 'webp' })
convert({ format: 'webp' })
```

Changing the dimensions of the desired image will always respect the aspect ratio of the original 
file. Thus, if you only pass the width OR the height the corresponding value will be calculated 
automatically. Passing both values, width and height, will adjust the higher one depending of the 
lower on, related to the original aspect ratio.

#### Example

```js
'assets/imgs/filepath.jpg'|theme|convert({ /* Options */ })
```

```html
<picture>
    <source srcset="{{ 'assets/imgs/original.jpg'|theme|convert({ format: 'webp' }) }}" media="(min-width: 1024)" type="image/webp" />
    <source srcset="{{ 'assets/imgs/original.jpg'|theme }}" media="(min-width: 1024)" />
    <source srcset="{{ 'assets/imgs/original.jpg'|theme|convert({ width: 1024, format: 'webp' }) }}" media="(min-width: 768px)" type="image/webp" />
    <source srcset="{{ 'assets/imgs/original.jpg'|theme|convert({ width: 1024 }) }}" media="(min-width: 768px)" />
    <source srcset="{{ 'assets/imgs/original.jpg'|theme|convert({ width: 768, format: 'webp' }) }}" type="image/webp" />
    <source srcset="{{ 'assets/imgs/original.jpg'|theme|convert({ width: 768}) }}" />
    <img src="{{ 'assets/imgs/original.jpg'|theme }}" alt="Image" />
</picture>
```


### picture

`picture` is registered as TWIG function and can be used to provide a full `<picture><source>` image 
structure, with defined source-set breakpoints.

#### Syntax

The `picture` function requires at least 2 arguments: The first one is the desired image URL path, 
the second one must be an object with the desired breakpoints, as described in detail below. You 
can declare the desired output formats as array as third argument (ex: `['webp', 'jpeg']`) and 
additional attributes for the `<img />` element as fourth one (ex.: `{ loading: 'lazy' }`).

```html
{{
    picture(
        '<image_path>'|theme,
        { /* <breakpoints> */ }
        [ /* <formats> */ ],
        { /* <img-attributes> */ }
    )
}}
```

You can pass the arguments also as "named arguments", by wrapping them into an object:

```html
{{
    picture({
        image:          '<image_path>'|theme,
        breakpoints:    { }
        formats:        [ ],
        attributes:     { }
    })
}}
```
