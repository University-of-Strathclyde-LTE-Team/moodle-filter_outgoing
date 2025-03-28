# Filter Outgoing
This plugin  arose due to an issue where a supplier lost control of a domain 
and the domain was taken over by a malicious actor.

As a result the embedded links to this now malicous domain were presented to all users.

## Installation
1. Download the plugin to your `/filters` directory.
2. Run the Moodle installation.
3. Access the filters' settings page and enable the "Outgoing Links Filter". 

   By default this is "applied to" just *Content*.
4. Use the Order arrows to move the filter to the top of the list.

   This will ensure that the filter is applied to the content as it comes 
   out of the Moodle database.

## Behaviour
By default the source content from the Moodle database will be scanned 
against a "deny list". If a match is found the link will be replaced with
a message. 

If the element found is an "inline" tag (such as an `<a>` tag) then the 
plugin will sanitise the content and replace the URI with a new one to a 
local end point indicating the content was filtered.

For block elements, the whole block tag will be removed and replaced with a 
warning message.

## Configuration
### Use Cache
By default the original content from the Moodle database will be hashed and 
if any substitutions are made, the new content will be stored in a cache 
against the original content's hash.

This means that if the same content is requested again, the cache will be used.

Because the Moodle filters are a chain, this cached content is what will be 
passed on to the next filter in the chain. 

**Note:** It is for this reason that the outgoing links filter should be 
first in the list, so that it can operate against as close to the original 
content as is possible.

## Deny List
Currently the deny list is hard coded.
