Algorithmic award

One thing that is old world is pricing. They always produce a sense of injustice. For example when you change your tax bracket.

It would be very easy to use an algorithmic progression. I find it interesting to popularise this practice by accompanying it with a graph that allows you to see the progression of discounts.

To do this, our friend is the logarithm. There is always a logarithm that can meet your needs :)

For quantity discounts, we can use this logarithm, which reproduces the same result as the scales:

log($n/50)*6.7

For time discounts, it is a question of making a difference according to the time at which the order is placed, knowing that each second that passes increases the price... I find this interesting. You will then have to deduct the non-working hours, but here is the preliminary calculation, from this simple straight line equation:

(3-$n)*10

It then remains to limit these results for negative results and beyond a maximum threshold.

![algos](https://user-images.githubusercontent.com/15765888/142649839-10258690-21aa-43e9-9511-464af548f579.png)

_Comparative table of results_

![algo1](https://user-images.githubusercontent.com/15765888/142649864-3952c814-dc22-4ae1-9e0a-273ed28b6c30.png)

_Graph of the logarithm of quantity discounts_

![algo2](https://user-images.githubusercontent.com/15765888/142649885-de60ed9d-d338-4fa7-86e0-f1a5a93987fc.png)

_Curve of additional costs due to delays_

## param "algo" in the Api

In the Api, we have added the "algo" variable, which is 0 by default, and can be set to 1 to use the algorithmic progression.
