<?php

namespace Imjoehaines\Flowder\Validator;

final class Validator
{
    /**
     * Validates the given array of data
     *
     * @param array $data
     * @return Result
     */
    public function validate(array $data)
    {
        $expectedColumns = array_keys(reset($data));

        $badColumns = array_reduce(
            array_keys($data),
            $this->validateColumns($expectedColumns, $data),
            []
        );

        if (!empty($badColumns)) {
            return Result::invalid($badColumns);
        }

        return Result::valid();
    }

    private function validateColumns(array $expectedColumns, array $data)
    {
        return function (array $badColumns, $index) use ($expectedColumns, $data) {
            $columns = array_keys($data[$index]);

            // use array_diff with array_keys instead of array_diff_key because
            // the latter still returns values and we want the keys returned
            $rowExtraColumns = array_diff($columns, $expectedColumns);

            if (!empty($rowExtraColumns)) {
                $badColumns[] = sprintf(
                    'Fixture at index %d has extra columns: "%s"',
                    $index,
                    implode('", "', $rowExtraColumns)
                );
            }

            $rowMissingColumns = array_diff($expectedColumns, $columns);

            if (!empty($rowMissingColumns)) {
                $badColumns[] = sprintf(
                    'Fixture at index %d is missing columns: "%s"',
                    $index,
                    implode('", "', $rowMissingColumns)
                );
            }

            return $badColumns;
        };
    }
}
