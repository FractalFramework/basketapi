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
