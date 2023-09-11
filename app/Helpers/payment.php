<?php

/**
 * Returns the discount amount.
 * Amount * Discount%
 *
 * @param $amount
 * @param $discount
 * @return float|int
 */
function calculateDiscount($amount, $discount)
{
    return $amount * ($discount / 100);
}

/**
 * Returns the amount after discount.
 * Amount - Discount$
 *
 * @param $amount
 * @param $discount
 * @return float|int
 */
function calculatePostDiscount($amount, $discount)
{
    return $amount - calculateDiscount($amount, $discount);
}

/**
 * Returns the inclusive taxes amount.
 * PostDiscount - PostDiscount / (1 + TaxRate)
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRate
 * @return float|int
 */
function calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRate)
{
    return calculatePostDiscount($amount, $discount) - (calculatePostDiscount($amount, $discount) / (1 + ($inclusiveTaxRate / 100)));
}

/**
 * Returns the amount after discount and included taxes.
 * PostDiscount - InclusiveTaxes$
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRates
 * @return float|int
 */
function calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates)
{
    return calculatePostDiscount($amount, $discount) - calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRates);
}

/**
 * Returns the amount of an inclusive tax.
 * PostDiscountLessInclTaxes * (Tax / 100)
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRate
 * @param $inclusiveTaxRates
 * @return float|int
 */
function calculateInclusiveTax($amount, $discount, $inclusiveTaxRate, $inclusiveTaxRates)
{
    return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($inclusiveTaxRate / 100);
}

/**
 * Returns the exclusive tax amount.
 * PostDiscountLessInclTaxes * TaxRate
 *
 * @param $amount
 * @param $discount
 * @param $exclusiveTaxRate
 * @param $inclusiveTaxRates
 * @return float|int
 */
function checkoutExclusiveTax($amount, $discount, $exclusiveTaxRate, $inclusiveTaxRates)
{
    return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($exclusiveTaxRate / 100);
}

/**
 * Calculate the total, including the exclusive taxes.
 * PostDiscount + ExclusiveTax$
 *
 * @param $amount
 * @param $discount
 * @param $exclusiveTaxRates
 * @param $inclusiveTaxRates
 * @return float|int
 */
function checkoutTotal($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates)
{
    return calculatePostDiscount($amount, $discount) + checkoutExclusiveTax($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates);
}

/**
 * Get the enabled payment processors.
 *
 * @return array
 */
function paymentProcessors()
{
    $paymentProcessors = config('payment.processors');

    foreach ($paymentProcessors as $key => $value) {
        // Check if the payment processor is not enabled
        if (!config('settings.' . $key)) {
            // Remove the payment processor from the list
            unset($paymentProcessors[$key]);
        }
    }

    return $paymentProcessors;
}



/*

This code snippet defines a series of functions that appear to be used for calculating various financial values in a checkout or purchasing system, including discounts, taxes, and the total checkout amount. Here's an overview of each function:

calculateDiscount($amount, $discount):

Calculates the discount amount based on the original amount and a given percentage discount.
Useful for determining how much is being taken off the original price.
calculatePostDiscount($amount, $discount):

Calculates the amount remaining after applying the discount.
Useful for finding the subtotal after a discount has been applied.
calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRate):

Calculates the amount of inclusive taxes based on the amount after the discount and a given inclusive tax rate.
Useful for figuring out the part of the price that consists of included taxes.
calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates):

Calculates the amount after discount and subtracting included taxes.
Useful for determining the net amount before exclusive taxes.
calculateInclusiveTax($amount, $discount, $inclusiveTaxRate, $inclusiveTaxRates):

Calculates a specific inclusive tax amount.
Useful for breaking down the inclusive taxes into specific components.
checkoutExclusiveTax($amount, $discount, $exclusiveTaxRate, $inclusiveTaxRates):

Calculates the exclusive tax amount based on the amount after discount and less inclusive taxes.
Useful for adding tax that is not included in the original price.
checkoutTotal($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates):

Calculates the total checkout amount, including exclusive taxes.
This function puts everything together to determine the final price a customer would pay.
paymentProcessors():

Retrieves the list of enabled payment processors from the configuration and returns them.
It removes any payment processors that are not enabled in the settings.
Useful for determining which payment options are available for a customer during checkout.
  
  
*/