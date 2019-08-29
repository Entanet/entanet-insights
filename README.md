# **Entanet Insights**
This is a package designed to lint over your projects code and
 provide insight on how things could be improved with visual data
 and traces of where lines could be improved in your code.
 This will cover: **Code**, **Complexity**, **Architecture** and **Style**.
 
 The package purpose is to be installed on your apps, and after running an artisan command it will pluck and format data, and then
 send the data to the "entaqa" app where the data can be visualised and updated.

# Installing

Require the Entanet Insights package
```
composer require entanet/entanet-insights --dev
```

Add the following to the config/app.php providers array
```
Superbalist\LaravelPubSub\PubSubServiceProvider::class
```

Publish the insights command
```
php artisan vendor:publish --provider="Entanet\Insights\InsightsServiceProvider"
```

Ensure the command is available:
```
app/console/commands/EntanetInsights.php
```

Ensure your env contains valid Kafka credentials and Kafka is set up
on your machine or within the container you're using.
```
KAFKA_BROKERS=kafka.broker.com
PUBSUB_CONNECTION=kafka
KAFKA_CONSUMER=ryan
```
# Usage

## Table of Contents

### Running
- [entaqa-app](#entaqa-app)
- [Listening](#listening)
- [Sending Scores](#sending-scores)


#### Entaqa App
Pull down the entaqa app from --repo--.
Within the project route, run:
```
docker-compose up --build -d
```
and navigate to
```
localhost:8080
```
This app uses Google auth for sign in.

#### Listening
To listen from the EntaQA app for scores, run:
```
docker-compose exec entaqa-app php artisan scores:listen
```

#### Sending Scores
Please ensure you have your "app name" set within your config/app.php file. 
This is responsible for naming the project within entaqa. 
Stick to repository naming conventions without spaces in, snake, camel and kebab case are all fine.

From within the project you have installed entanet-insights on,
run
```
php artisan entanet:insights
```
You should see "Data Sent!" as a confirmation.

Now, if you refresh your entaqa app, your app will be there with 
visual data on how to improve it, and if you click the widgets for
Code, Complexity, Architecture or Style, you can drill down
into specific insight on how to improve your app from that particular 
stand point.


Enjoy!