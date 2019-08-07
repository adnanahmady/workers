<?php

namespace Worker\Traits;

use Worker\Exceptions\InvalidFieldException;
use Worker\Models\DetailRelation;
use Worker\Models\Driver;
use Worker\Models\Passenger;
use Worker\Models\SamanTransactionDocument;

trait CheckTransactionTrait
{
    /**
     * Checks if Users Wallet is enough
     *
     * @param      $data
     * @param      $transaction
     * @param bool $plus
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function checkWallet($data, $transaction, $plus = FALSE)
    {
        if (preg_match('/^70/', $data['tafzil']))
        {
            $accountCheck = Passenger::checkWallet(['detail_code' => $data['tafzil']], $data['amount'], $plus);
        }
        else
        {
            $accountCheck = Driver::checkWallet(['detail_code' => $data['tafzil']], $data['amount'], $plus);
        }

        if (!$accountCheck)
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => (
                    preg_match('/^70/', $data['tafzil']) ?
                        'passengers' :
                        'drivers'
                    ) . " amount with tafzil \"{$data['tafzil']}\" is not enough"
                ]],
                ['upsert' => FALSE]
            );

            throw new InvalidFieldException(
                (preg_match('/^70/', $data['tafzil']) ?
                    'passengers' :
                    'drivers') . " amount with tafzil \"{$data['tafzil']}\" is not enough"
            );
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function checkReferenceNumber($data)
    {
        if (!empty($data["referenceNumber"]))
        {
            throw new InvalidFieldException('referenceNumber is not empty');
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function amountValidation($data, $transaction)
    {
        if (!preg_match('/\d+/', $data['amount']))
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => "amount value \"{$data['amount']}\" is not valid"]],
                ['upsert' => FALSE]
            );
            throw new InvalidFieldException("amount value \"{$data['amount']}\" is not valid");
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function isTafzilSet($data, $transaction)
    {
        if (!isset($data['tafzil']))
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => 'tafzil not found']],
                ['upsert' => FALSE]
            );
            throw new InvalidFieldException('tafzil not found');
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function isTafzilEmpty($data, $transaction)
    {
        if (empty($data['tafzil']))
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => 'tafzil is invalid']],
                ['upsert' => FALSE]
            );
            throw new InvalidFieldException('tafzil is invalid');
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function checkUser($data, $transaction)
    {
        if (! DetailRelation::getUser(['detail_code' => $data['tafzil']]))
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception' => "tafzil \"{$data['tafzil']}\" does not exist"]],
                ['upsert' => FALSE]
            );
            throw new InvalidFieldException("tafzil \"{$data['tafzil']}\" does not exist");
        }
    }

    /**
     * @param $data
     *
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    protected function checkSanadpardazTrackerId($data, $transaction)
    {
        if (!empty($data["trackerId_sanadpardaz"]))
        {
            $transaction::updateOne(
                ['_id' => $data['_id']],
                ['$set' => ['exception_sanadpardaz' => 'trackerId_sanadpardaz is not empty']],
                ['upsert' => FALSE]
            );
            throw new InvalidFieldException('trackerId_sanadpardaz is not empty');
        }
    }
}