# web
Web objects and widgets for Plinct app

write on array or json and output in HTML

>## basic element
```
[ 
  "tag" => "element tag", 
  "attributes" => [ "name" => "value" ], 
  "content" => "string" or [other element or object: array], 
  "href" => "path/to/" 
]
```
>## Objects
>There are three types: image, figure, picture
```
[
  "object" => "image",
  "attributes => [ 
    "class" => "image-styles",
    "id" => "image-id",
    "name" => "value" 
  ],
  "src" => "/path/to/image.ext",
  "href" => "/path/to/link",
  "width" = number,
  "height = number
  "attributesHref = [array]
],
[
  "object" => "figure",
  "attributes" => [ "name" => "value" ],
  "src" => "/path/to/image.ext",
  "href" => "/path/to/link",
  "width" = number,
  "height = number,
  "caption" = "string",
  "attributesImg = [array],
  "attributesHref = [array]
],
[
  "object" => "picture",
  "attributes" => [
    "class" => "image-styles",
    "id" => "image-id"
  ],
  "src" => "/path/to/image.ext",
  "href" => "/path/to/link",
  "sources" => [
    [ "width" => mumber, "height" => proportional number ],
    [ "width" => 720, "height" => 0.75 ],
    [ "width" => 1024, "height" => 0.5 ]
  ],
  "content" => another object or element
]
```
And more

* Breadcrumb function
* Scroll up button
* form widgets
