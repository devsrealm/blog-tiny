<?php

namespace App;

use Pimcore\Model\DataObject\BlogPost;
use Symfony\Component\HttpFoundation\Request;

class Helper
{
    private static array $urlParams = [];

    /**
     * Function to generate an excerpt from the content
     * @param string $content
     * @param int $length
     * @param bool $words
     * @return string
     */
    public static function GenerateExcerpt(string $content, int $length = 100, bool $words = false): string
    {
        # Remove any HTML tags from the content
        $excerpt = strip_tags($content);

        # Limit the excerpt to the desired length (characters or words)
        if ($words) {
            $excerptWords = preg_split('/\s+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY);
            $excerpt = implode(' ', array_slice($excerptWords, 0, $length));
        } else {
            $excerpt = substr($excerpt, 0, $length);
        }

        # Add "..." at the end of the excerpt if the content was truncated
        if (strlen($content) > strlen($excerpt)) {
            $excerpt .= '...';
        }

        return $excerpt;
    }


    /**
     * @throws \Exception
     */
    public static function GetBlogPosts(Request $request, callable $listing = null): object
    {
        $params = [];
        $queryString = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($queryString, $params);
        self::setURLParams($params);

        $configInternal = [
            'orderKey' => 'date',
            'order' => 'desc',
            "limit" => 10,
        ];
        $posts = BlogPost::getList($configInternal);
        $perPage = $posts->getLimit();

        # Very Simple Search To Conform With The Test Assignment
        if(!empty(trim($request->get('search')))){
            $searchTerm = $request->get('search');
            $posts->addConditionParam("Title LIKE :search OR Content LIKE :search", ["search" => "%$searchTerm%"]);
        }

        if ($listing){
            $listing($posts);
        }

        $totalPages = (int)ceil($posts->getTotalCount() / $perPage);
        # current page - The page the user is currently on, if we can't find the page number, we default to the first page
        $page = (int)$request->get('page', 1);
        #
        # Get the Offset based on the current page
        # The offset would determine the numbers of rows to skip before
        $offset = ($page - 1) * $perPage;
        $posts->setOffset($offset);

        #
        # ARRANGE THE PAGINATION RESULT
        return static::ArrangePagination(
            data: $posts,
            page: $page,
            totalPages: $totalPages,
            perPage: $perPage);
    }

    /**
     * @inheritDoc
     */
    protected static function getRequestURL(): string
    {
        $url = strtok($_SERVER['REQUEST_URI'], '?');
        ## D preg_replace converts multiple slashes to one.
        ## FILTER_SANITIZE_URL remove illegal chars from the url
        ## rtrim remove slash from the end e.g /name/book/ becomes  /name/book
        return rtrim(filter_var(preg_replace("#//+#", "\\1/", $url), FILTER_SANITIZE_URL), '/');
    }

    protected static function getRequestURLWithQueryString(): string
    {
        $queryString = http_build_query(self::getURLParams());
        $urlPathWithoutQueryString = self::getRequestURL();
        return $urlPathWithoutQueryString . '?' . $queryString;
    }


    /**
     * @param string $queryString
     * e.g "page=55" or "page=55&id=600" (you use the & symbol to add more query key and value)
     */
    protected static function appendQueryString(string $queryString): void
    {
        $params = [];
        parse_str($queryString, $params);
        $params = [...self::getURLParams(), ...$params];
        if(count($params) > 0) {
            self::setURLParams($params);
        }
    }


    /**
     * @param $data
     * @param $page
     * @param $totalPages
     * @param $perPage
     * @return object
     * @throws \Exception
     */
    private static function ArrangePagination($data, $page, $totalPages, $perPage): object
    {
             self::appendQueryString("page=1");
             $first_page_url = self::getRequestURLWithQueryString();

             $next_page_url = null;
             if ($page != $totalPages && $page <= $totalPages){
                 $pageNext = $page + 1;
                 self::appendQueryString("page=$pageNext");
                 $next_page_url = self::getRequestURLWithQueryString();
             }

            $prev_page_url = null;
            if ($page > 1 && $page <= $totalPages){
                $pagePrev = $page - 1;
                self::appendQueryString("page=$pagePrev");
                $prev_page_url = self::getRequestURLWithQueryString();
            }

        return (object)[
            'current_page' => (int)$page,
            'data' => $data,
            'from' => 1,
            'first_page_url' => $first_page_url,
            'next_page_url' => $next_page_url,
            'prev_page_url' => $prev_page_url,
            'prev_page' => ($page > 1 && $page <= $totalPages) ? $page - 1: null,
            'next_page' => ($page != $totalPages && $page <= $totalPages) ? $page + 1: null,
            'last_page' => $totalPages,
            'per_page' => $perPage,
            'to' => $totalPages,
            'total' => $totalPages,
            'has_more' => !(((int)$page === $totalPages)),
        ];
    }

    /**
     * @return array
     */
    public static function getUrlParams(): array
    {
        return self::$urlParams;
    }

    /**
     * @param array $urlParams
     */
    public static function setUrlParams(array $urlParams): void
    {
        self::$urlParams = $urlParams;
    }

}
