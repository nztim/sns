# SNS

Package to receive, verify and process SNS webhooks into Events.

### Installation

* `composer require nztim/sns`

### Usage

* Create a route to receive SNS requests, connect it to `NZTim\SNS\Examples\WebhookController` or your own handler
* Handle SNS events as required: `SubscriptionConfirmationEvent`, `NotificationEvent`, and `UnsubscribeConfirmationEvent`

