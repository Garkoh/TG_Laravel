from aiogram.types import InlineKeyboardMarkup, InlineKeyboardButton

settings = InlineKeyboardMarkup(
    inline_keyboard=[
        [InlineKeyboardButton(
            text='Перейти в приложение',
            url='0.0.0.0:8000/'
        )]
    ]
)
