# terminus-wp-clone-content
A plugin for Terminus that clones WP posts from one site.env to another 

Terminus WP Clone Content
A Terminus plugin that adds the wpclonecontent command to facilitate cloning WordPress content between environments on Pantheon.

Disclaimer
While this script has worked well for us your mileage may vary due to local machine configuration. 
This repository is provided without warranty or direct support. Issues and questions may be filed in GitHub but their resolution is not guaranteed.

Installation
Installing with Composer
`composer -n create-project mcdwayne/terminus-wp-clone-content:^1 ~/.terminus/plugins/terminus-wp-clone-content`

Manual installation
Clone this project into your Terminus plugins directory found at $HOME/.terminus/plugins. If the $HOME/.terminus/plugins directory does not exists you can safely create it. You will also need to run composer install in the plugin directory after cloning it. See installing Terminus plugin for details.

Requirements
Terminus 1.8.0 or greater

Usage
` terminus wpclonecontent <originsite>.<originenv> <targetsite>.<targetenv> <postid> `

example: `terminus wpclonecontent examplesitename.dev exampleproductionsite.test 555` which assumes there is a post numbered 555 on dev  

