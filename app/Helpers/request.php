<?php

/**
 * Selects and caches a proxy.
 *
 * @return null|string
 */
function getRequestProxy()
{
    // If request proxies are set
    if (!empty(config('settings.request_proxy'))) {
        // Check if there's a cached proxy already
        if (config('settings.request_cached_proxy')) {
            $proxy = config('settings.request_cached_proxy');
        } else {
            // Select a proxy at random
            $proxies = preg_split('/\n|\r/', config('settings.request_proxy'), -1, PREG_SPLIT_NO_EMPTY);
            $proxy = $proxies[array_rand($proxies)];

            // Cache the selected proxy
            config(['settings.request_cached_proxy' => $proxy]);
        }

        return $proxy;
    }

    return null;
}

/*

    This code snippet defines a function called getRequestProxy(), which is designed to select and cache a proxy for making HTTP requests. Here's a breakdown of what it does:

    Check if request proxies are set: The function first checks whether any proxies are defined in the application's configuration under settings.request_proxy. If proxies are configured, the function continues.

    Check for a Cached Proxy: If a proxy has been previously cached (stored) in the configuration under settings.request_cached_proxy, it's retrieved and returned.

    Select and Cache a Random Proxy: If no cached proxy is found, the function takes the proxies defined in settings.request_proxy, splits them into an array (separating them by newline characters), and then randomly selects one. This selected proxy is then cached in the configuration so that it can be reused in subsequent calls.

    Return the Proxy or Null: The selected proxy is returned, or if no proxies are configured, null is returned.

    This function is useful in scenarios where an application needs to make HTTP requests through various proxy servers, either for load balancing, anonymity, or bypassing regional restrictions. By caching the selected proxy, it can ensure that subsequent requests within the same session or context are sent through the same proxy server, maintaining consistency.


*/