# Application for managing the shopping cart via an Api

## Summary

Five actions have been determined

- create a basket
- delete a basket
- add an item to the basket
- view cart
- check the status of the basket, which returns the price

## How it works

The api is queried via a url request containing expected variables, and it returns a response in Json.

The data is stored in a session variable, identified by the $ecommerce_id and $customer_id.

## Price calculation

The default mode is to use scales.
Other modes can be added to choose to use other calculation modes.

## Application

There are three entries in the application:_

- via the url, by calling: /api/cartapi/{action}&{command}
- via the browser, at the url /cartapi
- via Ajax, from the interface

### Structure of the application

The code is read from the bottom to the top.
Each function calls an antecedent function.

- The call from the Url calls the api() function
- The interface calls content()
- The ajax commands call call()

The data received is validated in the validation() function, and errors are returned.

Prices are calculated in the calculate_price() function, which receives the data for an item.

Prices are calculated en masse for multiple items from the global_result() function, which calls calculate_price() iteratively.

All results are constructed via render_results() which can return status types 200, 400, 404, etc.

### Testing

The functions are built with a single variable, which is an array. Each function receiving only a $p variable is reachable independently of the others via ajax. This is done from an external class named ::unit, which requires superadmin rights.

The interface allows you to test renderings by playing with variables, create, add, check or delete baskets.

Examples are provided in order to perform these tests.
# Pixartprinting Challenge: Cart API

_*pixartprinting-challenge-cart-api*_

Basket API Code challenge: part of the interview process at Pixartprinting

_Dear candidate,_

_*We hope you are excited to take this challenge, as much as we will be while reviewing your code*_ :smile:

## Forewords

Take your time to solve this challenge: time is important but not the most...

You don't need to rush into delivering us a solution, but it can't also take forever.

We will check when you access this repo, when you clone it or fork it, and time between commits.

As soon as you fork this repo: add a CONTRIBUTING.md file push it, and then share the repo with us

## Cart Challenge description

We want you to create a REST API to implement the following business requirements collected from our e-commerce Cart use case session.
We are a "web to print" company: customers connect to our e-commerce choose products, select number of copies to print (quantity) and a delivery date. Add to Cart and checkout.

To have an idea on how we add to cart products, selecting: quantity/date, please have look at a live example: [Product Page](https://www.pixartprinting.co.uk/digital-litho-printing/printing-business-cards/)

### Cart creation

We create cart by specifying a `ecommerce id` , `customer id`, a `status`, `created_at`, `updated_at`
Cart `status` values can be : `created`, `building` , `checkout`

### Add Items to Cart

We add items to Cart by providing `ecommerce id`, `customer id` and `item list`

Each entity in `item list` has the following details:

`product sku`, `product name`, `file type`, `quantity`, `delivery date`.

For this challenge consider:

The `file type` as a string value indicating the file's MIME type. (This is the customer file artwork that will be uploaded attached to a cart item)
The `delivery date` is the choosen date for delivery by a customer
The `quantity` is the number of items for the same product

### View a Cart

The client should be able to query an API endpoint checking what's inside the Cart  
API will return:
Cart `ecommerce id`, `customer id`, `created_at` the `status`, eventually a total `price`

The `item list` with `product sku`, `product name`, `file type`, `quantity`, `delivery date`.
The calculated `price` for the item based on the policies below.

### Cart checkout

The customer will checkout the cart, the API will set the Cart `date checkout`
and will calculate and set the final price for the cart

### Price policies calculation

- Let's assume all our products on our catalog have a same standard standard base price of 1,00€ per item
- When you buy more items of same `products sku` :
  - no discount is applied for quantities until 100
  - a 5% discount is applied for quantities above 100
  - a 10% discount is applied for quantities above 250
  - a 15% discount is applied for quantities above 500
  - a 20% discount is applied for quantities above 1000
- When the choosen delivery date is (compared to Cart date checkout ):
  - within 24h price increments 30%
  - within 48h price increments 20%
  - within 72h price increments 10%
  - within 1 week price have no increment
- For PDF type formats (Adobe Acrobat Reader) the price is 15% more than the standard one.
- For PSD type formats (Adobe Photoshop) the price is 35% more than the standard one.
- For AI type formats (Adobe Illustrator) the price is 25% more than the standard one.

## What's desiderable from this project

- Technical stack: You can choose your favourite stack of technologies for this project
- Readability: Create clean code, following standards and good principles of code design. Comment code where's needed.
- Compliance: to the specs
- Testability: easy to understand and test
- Document: Providing a summary of the activity
- Feedback: pls leave us some comments on what you think about the test

**_Have fun coding this challenge!_**

Please let us know when you are done and have committed on the gitHub repo

Do not hesitate to contact us for any question about this challenge,
reaching out to your contact person in Pixartprinting!

## Url

http://logic.ovh/cartapi


