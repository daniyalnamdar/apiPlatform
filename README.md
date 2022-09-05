# Hello There 
## ***Welcome to my GitHub page***

This Repository is based on the API PLATFORM Tutorial from [SymfonyCast.](https://symfonycasts.com/screencast/api-platform)

*This is the first part of the three API PLATFORM courses available.* 

To start, you just need to clone the code and run 
```
composer install
```

The Second part of this series ***Security*** will be updated on the branch with the same name.
Please make sure that you have the following files on your local \
***package.json*** \
***webpack.config.js*** \
***base.html.twig*** \
***homepage.html.twig*** \
***FrontendController.php*** \


Besides, run the following command 
```
composer require encore
```
```
composer require webpack
```
```
npm install
```
```
yarn install
```
```
yarn encore dev --watch
```
If you are facing The **Error: error:0308010C:digital envelope routines::unsupported**
you can either downgrade Node.JS to long term support version 16.14.0 or run the following command
```
export NODE_OPTIONS=--openssl-legacy-provider
```
