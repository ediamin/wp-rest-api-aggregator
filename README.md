# WP REST API Aggregator

### Description
Get multiple WordPress REST API response in a single request. Using WordPress REST API, we usually fetch data for only one resource. So, for a single page, we may have request following four APIs:
```
https://example.com/wp/v2/posts/1
https://example.com/my-plugin/v1/header-menu
https://example.com/my-plugin/v1/footer-menu
https://example.com/my-plugin/v1/right-sidebar
```

With the help of this plugin, similar to GraphQL requests, we can fetch all necessary data for a single page in a single API request to the `/wp-rest-api-aggregator/v1/aggregate` endpoint with this params,
```
{
    "requests": [
        "/wp/v2/posts/1",
        "/my-plugin/v1/header-menu",
        "/my-plugin/v1/footer-menu",
        "/my-plugin/v1/right-sidebar"
    ]
}
```

This will give a response like this,
```
{
    "/wp/v2/posts/1": {
        "data": { ... },
        "_headers": { ... }
    },
    "/my-plugin/v1/header-menu": {
        "data": { ... },
        "_headers": { ... }
    },
    "/my-plugin/v1/footer-menu": {
        "data": { ... },
        "_headers": { ... }
    },
    "/my-plugin/v1/right-sidebar": {
        "data": { ... },
        "_headers": { ... }
    }
}
```
`data` and `_headers` properties contain the related responses and headers for the endpoints.
